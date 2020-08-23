<h2><?= h($login_user->user->username) ?>の評価</h2>

<h2><?php echo $review_avg ?></h2>

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
                foreach ($reviews as $review) : ?>
               <?php        
               $i=1;

               for($i;$i<=$reviewer_count;$i++){     
               if(h($review->reviewer_id) === h($reviewers_name[$i-1]->id)){ ?>
                <td><?= h($reviewers_name[$i-1]->username) ?></td>
               <?php }};?>
                <td><?= h($review->review) ?></td>
                <td><?= h($review->comment) ?></td>
        </tr>
        <?php
                endforeach; ?>

    </tbody>
</table>