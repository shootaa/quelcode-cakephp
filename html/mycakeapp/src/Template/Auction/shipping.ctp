<!-- 発送先入力フォーム -->
<?php
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
    <!-- 発送待ち -->
    <?php if ($authuser['id'] === $bidinfo['user_id'] && $bidinfo['name'] && !$bidinfo['is_shipped']) { ?>
        <p>発送までお待ちください</p>
    <?php }; ?>
    <div class="related">
        <!-- 受取完了フォーム -->
        <?php if ($authuser['id'] === $bidinfo['user_id'] && $bidinfo['is_shipped'] && !$bidinfo['is_received']) { ?>
            <?= $this->Form->create($bidinfo, [
                'type' => 'post',
                'url' => [
                    'controller' => 'Auction',
                    'action' => 'shipping'
                ]
            ]); ?>
            <?php
            echo $this->Form->hidden('Bidinfo.id');
            echo $this->Form->hidden('Bidinfo.is_received', ['value' => 1]); ?>
            <?= $this->Form->button('受取完了'); ?>
            <?= $this->Form->end(); ?>

        <?php  }; ?>



    </div>
    <!-- 発送先の入力待ち -->
    <?php if ($authuser['id'] === $biditem['user_id'] && !$bidinfo['name']) { ?>
        <p>発送先が入力されるまでお待ちください</p>
    <?php }; ?>
    <!-- 発送先の入力完了 -->
    <?php
    if ($authuser['id'] === $biditem['user_id'] && !$bidinfo['is_shipped']) {
    ?>
        <div class="related">
             <h2>発送先</h2>
            <table>
                <?= $this->Html->tableHeaders(["name", "address", "phone"]) ?>
                <?= $this->Html->tableCells([[$bidinfo['name'], $bidinfo['address'], $bidinfo['phone']]]) ?>
            </table>
            <!-- 発送完了フォーム -->
            <?php if ($bidinfo['name'] && !$bidinfo['is_shipped']) { ?>
                <?= $this->Form->create($bidinfo, [
                    'type' => 'post',
                    'url' => [
                        'controller' => 'Auction',
                        'action' => 'shipping'
                    ]
                ]); ?>
                <?php
                echo $this->Form->hidden('Bidinfo.id');
                echo $this->Form->hidden('Bidinfo.is_shipped', ['value' => 1]); ?>
                <?= $this->Form->button('発送完了'); ?>
                <?= $this->Form->end(); ?>

        </div>

<?php }
        }; ?>
<?php if ($authuser['id'] === $biditem['user_id'] && $bidinfo['is_shipped'] && !$bidinfo['is_received']) { ?>
    <p>受取完了までお待ちください</p>
<?php }; ?>
<!-- 受取完了ボタン押下後に両者入力可能になる -->
<!-- 評価フォーム -->
<div class="related" style="width:70%">
    <?php if ($bidinfo['is_shipped'] && $bidinfo['is_received'] && !$review['bidinfo_id']) { ?>
        <h2>相手への評価を入力する</h2>
        <?= $this->Form->create($bidinfo, [
            'type' => 'post',
            'url' => [
                'controller' => 'Auction',
                'action' => 'reviewadd'
            ]
        ]); ?>
        <?php
        echo $this->Form->hidden('Reviews.bidinfo_id', ['value' => $bidinfo['id']]);
        echo $this->Form->hidden('Reviews.reviewer_id', ['value' => $authuser['id']]);
        echo $this->Form->hidden('Reviews.reviewed_id', ['value' => $biditem['user_id']]);
        ?>
        <p>相手への評価を１（低）〜５（高）で入力してください。（必須）</p>
        <?php
        echo $this->Form->input('Reviews.review', ['type' => 'radio', 'options' => [['value' => 1, 'text' => 1], ['value' => 2, 'text' => 2], ['value' => 3, 'text' => 3], ['value' => 4, 'text' => 4], ['value' => 5, 'text' => 5]]]);
        ?>
        <p>相手への評価コメントを入力してください。</p>

        <?php
        echo $this->Form->control('Reviews.comment');
        ?>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>

    <?php }; ?>
    <!-- 評価を一度送信している場合 -->
    <?php if ($bidinfo['is_received'] && $review['bidinfo_id']) { ?>
        <p>評価は送信しました。</p>
    <?php }; ?>
</div>