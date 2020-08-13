<?php
// var_dump($authuser['id']);
// var_dump($bidinfo['user_id']);
// var_dump($biditem['user_id']);

if ($authuser['id'] === $bidinfo['user_id'] && !$bidinfo['name']) {
?>
    <div class="related">
        <h2>発送先を入力する</h2>
        <?php
        ?>
        <?= $this->Form->create($bidinfo, [
            'type' => 'post',
            'url' => [
                'controller' => 'Auction',
                'action' => 'shipping'
            ]
        ]) ?>
        <fieldset>
            <legend>※商品名と終了日時を入力：</legend>
            <?php
            echo $this->Form->hidden('Bidinfo.id');
            echo $this->Form->hidden('Bidinfo.biditem_id');
            echo $this->Form->hidden('Bidinfo.price');
            echo $this->Form->hidden('Bidinfo.user_id');
            echo $this->Form->control('Bidinfo.name');
            echo $this->Form->control('Bidinfo.address');
            echo $this->Form->control('Bidinfo.phone');
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')); ?>
        <?= $this->Form->end(); ?>
    <?php  }; ?>
    </div>
    <div class="related">
        <?php if ($authuser['id'] === $bidinfo['user_id'] && $bidinfo['is_shipped'] && !$bidinfo['is_received']) { ?>
            <?= $this->Form->create($bidinfo, [
                'type' => 'post',
                'url' => [
                    'controller' => 'Auction',
                    'action' => 'shipping'
                ]
            ]); ?>
            <fieldset>
                <?php
                echo $this->Form->hidden('Bidinfo.id');
                echo $this->Form->hidden('Bidinfo.is_received', ['value' => 1]); ?>
            </fieldset>
            <?= $this->Form->button('受取完了'); ?>
            <?= $this->Form->end(); ?>

        <?php  }; ?>



        <!-- p 落札者かつフォームを入力したらフォーム非表示?> -->
        <!-- p 落札者かつ発送ボタン押下ずみ?> -->
        <!-- 出品者のみに表示。発送完了ボタン押下後に入力可能 -->
    </div>
    <?php
    if ($authuser['id'] === $biditem['user_id'] && !$bidinfo['is_shipped']) {
    ?>
        <div class="related">
            <!-- <p 出品者 ?> -->
             <h2>発送先</h2>
            <table>
                <?= $this->Html->tableHeaders(["name", "address", "phone"]) ?>
                <?= $this->Html->tableCells([[$bidinfo['name'], $bidinfo['address'], $bidinfo['phone']]]) ?>
            </table>
            <!-- 落札者のみに表示。発送先入力後に入力可能フォーム -->
            <?php if ($bidinfo['name'] && !$bidinfo['is_shipped']) { ?>
                <?= $this->Form->create($bidinfo, [
                    'type' => 'post',
                    'url' => [
                        'controller' => 'Auction',
                        'action' => 'shipping'
                    ]
                ]); ?>
                <fieldset>
                    <?php
                    echo $this->Form->hidden('Bidinfo.id');
                    echo $this->Form->hidden('Bidinfo.is_shipped', ['value' => 1]); ?>
                </fieldset>
                <?= $this->Form->button('発送完了'); ?>
                <?= $this->Form->end(); ?>

        </div>


<?php }
        }; ?>

<!-- 受取完了ボタン押下後に両者入力可能になる -->
<!-- p 受取完了ボタンが押されている?> -->
<div class="related" style="width:70%">
    <?php if ($bidinfo['is_shipped'] && $bidinfo['is_received']) { ?>
        <h2>相手への評価を入力する</h2>
        <?= $this->Form->create($review, [
            'type' => 'post',
            'url' => [
                'controller' => 'Auction',
                'action' => 'reviewadd'
            ]
        ]); ?>
        <fieldset>
            <legend>※商品名と終了日時を入力：</legend>
            <?php
            echo $this->Form->hidden('Reviews.reviewer_id', ['value' => $authuser['id']]);
            echo $this->Form->hidden('Reviews.reviewed_id', ['value' => $biditem['user_id']]);

            ?>
            <p>相手への評価を１（低）〜５（高）で入力してください。（必須）</p>
            <?php
            echo $this->Form->control('Reviews.review');
            ?>
            <p>相手への評価コメントを入力してください。</p>

            <?php
            echo $this->Form->control('Reviews.comment');
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    <?php }; ?>
</div>
<!-- <p>評価を送信しました。</p> -->