<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Products;

/**
 * ProductsSearch represents the model behind the search form of `common\models\Products`.
 */
class ProductsSearch extends Products
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'marketplace_id', 'store_id'], 'integer'],
            [['sessia_product_id', 'marketplace_product_id', 'marketplace_product_id_2'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Products::find();

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
            'id' => $this->id,
            'marketplace_id' => $this->marketplace_id,
            'store_id' => $this->store_id,
        ]);

        $query->andFilterWhere(['like', 'sessia_product_id', $this->sessia_product_id])
            ->andFilterWhere(['like', 'marketplace_product_id', $this->marketplace_product_id])
            ->andFilterWhere(['like', 'marketplace_product_id_2', $this->marketplace_product_id_2]);

        return $dataProvider;
    }
}
