<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Bidinfo Model
 *
 * @property \App\Model\Table\BiditemsTable&\Cake\ORM\Association\BelongsTo $Biditems
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\BidmessagesTable&\Cake\ORM\Association\HasMany $Bidmessages
 *
 * @method \App\Model\Entity\Bidinfo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Bidinfo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Bidinfo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Bidinfo|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Bidinfo saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Bidinfo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Bidinfo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Bidinfo findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ReviewsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('Reviews');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'reviewer_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'reviewed_id',
            'joinType' => 'INNER',
        ]);

    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('reviewer_id')
            ->requirePresence('reviewer_id', 'create')
            ->notEmptyString('reviewer_id');
            
            $validator
            ->integer('reviewed_id')
            ->requirePresence('reviewed_id', 'create')
            ->notEmptyString('reviewed_id');
            
            $validator
            ->integer('review')
            ->requirePresence('review', 'create')
            ->maxLength('review', 1)
            ->notEmptyString('review');

            $validator
            ->scalar('comment')
            ->requirePresence('comment', 'create')
            ->allowEmptyString('comment', null, 'create');
            
            $validator
            ->boolean('is_shipped')
            ->requirePresence('is_shipped', 'create')
            ->allowEmptyString('phone', null, 'create');
            
            $validator
            ->boolean('is_received')
            ->requirePresence('is_received', 'create')
            ->allowEmptyString('phone', null, 'create');

            $validator
            ->dateTime('created')
            ->requirePresence('created', 'create')
            ->notEmptyDateTime('created');



        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['reviewer_id'], 'Users'));
        $rules->add($rules->existsIn(['reviewed_id'], 'Users'));

        return $rules;
    }
}
