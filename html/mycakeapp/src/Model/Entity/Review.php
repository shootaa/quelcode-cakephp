<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Review Entity
 *
 * @property int $id
 * @property int $reviewer_id
 * @property int $reviewed_id
 * @property int $review
 * @property string $comment
 * @property \Cake\I18n\Time $created
 *
 * @property \App\Model\Entity\Biditem $biditem
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Bidmessage[] $bidmessages
 */
class Review extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'bidinfo_id' => true,
        'reviewer_id' => true,
        'reviewed_id' => true,
        'review' => true,
        'comment' => true,
        'created' => true,
        'user' => true,
    ];
}
