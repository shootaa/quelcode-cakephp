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
class BidinfoTable extends Table
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

        $this->setTable('bidinfo');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Biditems', [
            'foreignKey' => 'biditem_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Bidmessages', [
            'foreignKey' => 'bidinfo_id',
        ]);
        $this->hasMany('Reviews', [
            'foreignKey' => 'bidinfo_id',
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
            ->integer('price')
            ->requirePresence('price', 'create')
            ->notEmptyString('price');
            
            $validator
            ->scalar('name')
            ->requirePresence('name', 'create')
            ->maxLength('name', 255)
            ->allowEmptyString('name', null, 'create');
            
            $validator
            ->scalar('address')
            ->requirePresence('address', 'create')
            ->maxLength('address', 255)
            ->allowEmptyString('address', null, 'create');

            $validator
            ->scalar('phone')
            ->requirePresence('phone', 'create')
            ->maxLength('phone', 255)
            ->allowEmptyString('phone', null, 'create');
            
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
        $rules->add($rules->existsIn(['biditem_id'], 'Biditems'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
