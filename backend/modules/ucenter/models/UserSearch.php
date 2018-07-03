<?php

namespace backend\modules\ucenter\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\ucenter\models\User;

/**
 * UserSearch represents the model behind the search form about `backend\modules\ucenter\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'birthday', 'underling_number', 'pay_points', 'address_id', 'created_at', 'updated_at', 'last_login', 'province', 'city', 'district', 'level', 'is_lock', 'first_leader', 'second_leader', 'third_leader'], 'integer'],
            [['username', 'mobile', 'email', 'access_token', 'auth_key', 'password_hash', 'password_reset_token', 'last_ip', 'qq', 'oauth', 'openid', 'avatar', 'nick_name', 'paypwd', 'password_plain'], 'safe'],
            [['user_money', 'frozen_money', 'distribut_money', 'discount', 'total_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'status' => $this->status,
            'birthday' => $this->birthday,
            'user_money' => $this->user_money,
            'frozen_money' => $this->frozen_money,
            'distribut_money' => $this->distribut_money,
            'underling_number' => $this->underling_number,
            'pay_points' => $this->pay_points,
            'address_id' => $this->address_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_login' => $this->last_login,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'level' => $this->level,
            'discount' => $this->discount,
            'total_amount' => $this->total_amount,
            'is_lock' => $this->is_lock,
            'first_leader' => $this->first_leader,
            'second_leader' => $this->second_leader,
            'third_leader' => $this->third_leader,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'last_ip', $this->last_ip])
            ->andFilterWhere(['like', 'qq', $this->qq])
            ->andFilterWhere(['like', 'oauth', $this->oauth])
            ->andFilterWhere(['like', 'openid', $this->openid])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like', 'paypwd', $this->paypwd])
            ->andFilterWhere(['like', 'password_plain', $this->password_plain]);
        return $dataProvider;
    }
}
