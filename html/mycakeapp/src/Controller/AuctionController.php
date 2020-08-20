<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event; // added.
use Exception; // added.

class AuctionController extends AuctionBaseController
{
	// デフォルトテーブルを使わない
	public $useTable = false;

	// 初期化処理
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Paginator');
		// 必要なモデルをすべてロード
		$this->loadModel('Users');
		$this->loadModel('Biditems');
		$this->loadModel('Bidrequests');
		$this->loadModel('Bidinfo');
		$this->loadModel('Bidmessages');
		$this->loadModel('Reviews');
		// ログインしているユーザー情報をauthuserに設定
		$this->set('authuser', $this->Auth->user());
		// レイアウトをauctionに変更
		$this->viewBuilder()->setLayout('auction');
	}

	// トップページ
	public function index()
	{
		// ページネーションでBiditemsを取得
		$auction = $this->paginate('Biditems', [
			'order' => ['endtime' => 'desc'],
			'limit' => 10
		]);
		$this->set(compact('auction'));
	}

	// 商品情報の表示
	public function view($id = null)
	{
		// $idのBiditemを取得
		$biditem = $this->Biditems->get($id, [
			'contain' => ['Users', 'Bidinfo', 'Bidinfo.Users']
		]);

		// オークション終了時の処理
		if ($biditem->endtime < new \DateTime('now') and $biditem->finished == 0) {
			// finishedを1に変更して保存
			$biditem->finished = 1;
			$this->Biditems->save($biditem);
			// Bidinfoを作成する
			$bidinfo = $this->Bidinfo->newEntity();
			// Bidinfoのbiditem_idに$idを設定
			$bidinfo->biditem_id = $id;
			// 最高金額のBidrequestを検索
			$bidrequest = $this->Bidrequests->find('all', [
				'conditions' => ['biditem_id' => $id],
				'contain' => ['Users'],
				'order' => ['price' => 'desc']
			])->first();
			// Bidrequestが得られた時の処理
			if (!empty($bidrequest)) {
				// Bidinfoの各種プロパティを設定して保存する
				$bidinfo->user_id = $bidrequest->user->id;
				$bidinfo->user = $bidrequest->user;
				$bidinfo->price = $bidrequest->price;
				$this->Bidinfo->save($bidinfo);
			}
			// Biditemのbidinfoに$bidinfoを設定
			$biditem->bidinfo = $bidinfo;
		}
		// Bidrequestsからbiditem_idが$idのものを取得
		$bidrequests = $this->Bidrequests->find('all', [
			'conditions' => ['biditem_id' => $id],
			'contain' => ['Users'],
			'order' => ['price' => 'desc']
		])->toArray();
		// オブジェクト類をテンプレート用に設定
		$this->set(compact('biditem', 'bidrequests'));
	}

	// 出品する処理
	public function add()
	{
		// Biditemインスタンスを用意
		$biditem = $this->Biditems->newEntity();
		// POST送信時の処理
		if ($this->request->is('post')) {
			// $biditemにフォームの送信内容を反映
			// $biditemを保存する

			$picture_data = $this->request->getData('picture');
			$picture_name =  pathinfo($picture_data['name'], PATHINFO_FILENAME);
			$extension = pathinfo($picture_data['name'], PATHINFO_EXTENSION);
			$picture_path = '../webroot/img/auction/';
			$tmp = $picture_name;
			$i = 0;
			while (file_exists($picture_path . $tmp . '.' . $extension)) {
				$tmp = $picture_name . '_' . $i;
				$i++;
			}
			$picture_name = $tmp . '.' . $extension;
			$picture_path = '../webroot/img/auction/' . $picture_name;
			move_uploaded_file($picture_data['tmp_name'], $picture_path);
			$data = array(
				'user_id' => $this->request->getData('user_id'),
				'name' => $this->request->getData('name'),
				'description' => $this->request->getData('description'),
				'picture_path' => $picture_name,
				'finished' => $this->request->getData('finished'),
				'endtime' => $this->request->getData('endtime'),
			);
			$biditem = $this->Biditems->patchEntity($biditem, $data);

			if ($this->Biditems->save($biditem)) {
				// 成功時のメッセージ
				$this->Flash->success(__('保存しました。'));
				// トップページ（index）に移動
				return $this->redirect(['action' => 'index']);
			}
			// 失敗時のメッセージ
			$this->Flash->error(__('保存に失敗しました。もう一度入力下さい。'));
		}
		// 値を保管
		$this->set(compact('biditem'));
	}

	// 入札の処理
	public function bid($biditem_id = null)
	{
		// 入札用のBidrequestインスタンスを用意
		$bidrequest = $this->Bidrequests->newEntity();
		// $bidrequestにbiditem_idとuser_idを設定
		$bidrequest->biditem_id = $biditem_id;
		$bidrequest->user_id = $this->Auth->user('id');
		// POST送信時の処理
		if ($this->request->is('post')) {
			// $bidrequestに送信フォームの内容を反映する
			$bidrequest = $this->Bidrequests->patchEntity($bidrequest, $this->request->getData());
			// Bidrequestを保存
			if ($this->Bidrequests->save($bidrequest)) {
				// 成功時のメッセージ
				$this->Flash->success(__('入札を送信しました。'));
				// トップページにリダイレクト
				return $this->redirect(['action' => 'view', $biditem_id]);
			}
			// 失敗時のメッセージ
			$this->Flash->error(__('入札に失敗しました。もう一度入力下さい。'));
		}
		// $biditem_idの$biditemを取得する
		$biditem = $this->Biditems->get($biditem_id);
		$this->set(compact('bidrequest', 'biditem'));
	}

	// 落札者とのメッセージ
	public function msg($bidinfo_id = null)
	{
		// Bidmessageを新たに用意
		$bidmsg = $this->Bidmessages->newEntity();
		// POST送信時の処理
		if ($this->request->is('post')) {
			// 送信されたフォームで$bidmsgを更新
			$bidmsg = $this->Bidmessages->patchEntity($bidmsg, $this->request->getData());
			// Bidmessageを保存
			if ($this->Bidmessages->save($bidmsg)) {
				$this->Flash->success(__('保存しました。'));
			} else {
				$this->Flash->error(__('保存に失敗しました。もう一度入力下さい。'));
			}
		}
		try { // $bidinfo_idからBidinfoを取得する
			$bidinfo = $this->Bidinfo->get($bidinfo_id, ['contain' => ['Biditems']]);
		} catch (Exception $e) {
			$bidinfo = null;
		}
		// Bidmessageをbidinfo_idとuser_idで検索
		$bidmsgs = $this->Bidmessages->find('all', [
			'conditions' => ['bidinfo_id' => $bidinfo_id],
			'contain' => ['Users'],
			'order' => ['created' => 'desc']
		]);
		$this->set(compact('bidmsgs', 'bidinfo', 'bidmsg'));
	}

	// 発送先のメッセージ
	public function shipping($bidinfo_id = null)
	{
		//落札した商品の情報を取得する
		$bidinfo = $this->Bidinfo->get($bidinfo_id);
		$this->set('bidinfo', $bidinfo);
		$biditem = $this->Biditems->get($bidinfo['biditem_id']);
		$this->set('biditem', $biditem);
		$review = $this->Reviews->find()->where(['bidinfo_id' => $bidinfo_id])->first();
		$this->set(compact('review'));
		//落札者、出品者以外がログインしたらindexへ飛ばす
		$login_user = $this->Auth->user();
		if (!($login_user['id'] === $bidinfo['user_id']) | !($login_user['id'] === $biditem['user_id'])) {
			return $this->redirect(['action' => 'index', $bidinfo_id]);
		};
		// POST送信時の処理
		if ($this->request->is('post')) {
			$data = $this->request->data['Bidinfo'];
			$bidinfo = $this->Bidinfo->get($data['id']);
			$this->Bidinfo->patchEntity($bidinfo, $data);
			// Bidinfoを保存
			if ($this->Bidinfo->save($bidinfo)) {
				$this->Flash->success(__('保存に成功しました。'));
			} else {
				$this->Flash->error(__('送信に失敗しました。もう一度入力下さい。'));
			}
		}
		try {
			// $bidinfo_idからBidinfoを取得する
			$bidinfo = $this->Bidinfo->get($bidinfo_id, ['contain' => ['Biditems']]);
		} catch (Exception $e) {
			$bidinfo = null;
		}
	}
	//評価トップページ
	public function reviewIndex()
	{

		// ページネーションでReviewsを取得
		$reviews = $this->paginate('Reviews', [
			'fields' => array('Users.username', 'Reviews.reviewed_id'),
			'group' => array('Users.username', 'Reviews.reviewed_id'),
			'limit' => 10,
			'contain' => ['Users']
		])->toArray();
		$this->set(compact('reviews'));
		$this->render('/Auction/reviewindex');
	}
	//評価詳細ページ
	public function reviewView($reviewed_id = null)
	{
		$login_user = $this->Reviews->find()->find('all', ['contain' => ['Users']])->where(['reviewed_id' => $reviewed_id])->first();
		$reviews = $this->Reviews->find('all', ['contain' => ['Users']])->where(['reviewed_id' => $reviewed_id])->toArray();
		$review = $this->Reviews->find('all');
		//評価合計と評価個数を取得
		$review_sum = $review->where(['reviewed_id' => $reviewed_id])->select(['review_sum' => $review->func()->sum('review')])->enableHydration(false)->first();
		$review_count = $this->Reviews->find()->where(['reviewed_id' => $reviewed_id])->count();
		$username = $this->Reviews->find('all', ['contain' => ['Users']])->where(['reviewed_id' => $this->Auth->user('id')])->toArray();
		//評価の平均値として小数点以下第二位を四捨五入
		$review_avg = round($review_sum['review_sum'] / $review_count, 1);
		$this->set(compact('login_user', 'username', 'reviews', 'review_avg'));
	}

	//評価送信ページ
	public function reviewAdd()
	{
		//POST送信時の処理
		if ($this->request->is('post')) {
			$data = $this->request->data['Reviews'];
			$error = (int)$data['review'];
			//例外値が入力された場合の処理
			$review_number = [1, 2, 3, 4, 5];
			if (!in_array($error, $review_number, true)) {
				$this->Flash->error(__('1〜5の数字で入力してください。'));
			};
			$review = $this->Reviews->newEntity($data);
			if ($this->Reviews->saveOrFail($review, false)) {
				$this->Flash->success(__('保存に成功しました。'));
				return $this->redirect(['action' => 'home']);
			} else {
				$this->Flash->error(__('送信に失敗しました。もう一度入力下さい。'));
			}
		}
		$this->set(compact('review'));
		$this->render('/Auction/shipping');
	}




	// 落札情報の表示
	public function home()
	{
		// 自分が落札したBidinfoをページネーションで取得
		$bidinfo = $this->paginate('Bidinfo', [
			'conditions' => ['Bidinfo.user_id' => $this->Auth->user('id')],
			'contain' => ['Users', 'Biditems'],
			'order' => ['created' => 'desc'],
			'limit' => 10
		])->toArray();
		$this->set(compact('bidinfo'));
	}

	// 出品情報の表示
	public function home2()
	{
		// 自分が出品したBiditemをページネーションで取得
		$biditems = $this->paginate('Biditems', [
			'conditions' => ['Biditems.user_id' => $this->Auth->user('id')],
			'contain' => ['Users', 'Bidinfo'],
			'order' => ['created' => 'desc'],
			'limit' => 10
		])->toArray();
		$this->set(compact('biditems'));
	}
}
