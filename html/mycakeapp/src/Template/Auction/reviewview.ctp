<h2><?= h($login_user->user->username) ?>の評価</h2>

<h2><?= h($review_avg['review']) ?></h2>

<h3>各ユーザーからの評価</h3>
<table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th class="main" scope="col"><?= $this->Paginator->sort('name') ?></th>
            <th class="main" scope="col"><?= $this->Paginator->sort('review') ?></th>
            <th class="main" scope="col"><?= $this->Paginator->sort('comment') ?></th>

        </tr>
    </thead>
    <tbody>

        <tr> <?php
                foreach ($review as $review) :
                    foreach ($username as $username) :
                        foreach ($comment as $comment) : ?>

                        <td><?= h($username->user->username) ?></td>
                        <td><?= h($review->review) ?></td>
                        <td><?= h($comment->comment) ?></td>
        </tr>
<?php endforeach;
                    endforeach;
                endforeach; ?>

    </tbody>
</table>