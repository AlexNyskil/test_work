<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Book;
use yii\helpers\ArrayHelper;

/**
 * BookSearch represents the model behind the search form of `common\models\Book`.
 */
class BookSearch extends Book
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'page_count', 'status'], 'integer'],
            [['title', 'isbn', 'published_date', 'thumbnail_url', 'short_description', 'long_description'], 'safe'],
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
        $query = Book::find();

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
            'page_count' => $this->page_count,
            'published_date' => $this->published_date,
            'status' => $this->status,
        ]);

        if (ArrayHelper::keyExists('category_id', $params)) {
            $query->andFilterWhere(['category_id' => $params['category_id']]);
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'isbn', $this->isbn])
            ->andFilterWhere(['like', 'thumbnail_url', $this->thumbnail_url])
            ->andFilterWhere(['like', 'short_description', $this->short_description])
            ->andFilterWhere(['like', 'long_description', $this->long_description]);

        return $dataProvider;
    }
}
