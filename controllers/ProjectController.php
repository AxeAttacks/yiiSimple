<?php
/**
 * Created by PhpStorm.
 * User: mvnosulenko
 * Date: 14.11.14
 * Time: 11:31
 */
class ProjectController extends CController
{
    public $seo;

    public function init() {
        $this->seo = Seo::model()->find('section=:section',array(':section'=>Yii::app()->params['seoItem']['project']));
    }

    public function actionIndex(){
        /************************************Slider****************************************************/
        $criteria = new CDbCriteria();
        $criteria->order = '`order` ASC';
        $criteria->condition = '`section` = '.Yii::app()->params['sliderItem']['project'];
        $slider = Slider::model()->findAll($criteria);
        if (!$slider){
            $criteria->condition = '`section` = '.Yii::app()->params['sliderItem']['work'];
            $slider = Slider::model()->findAll($criteria);
        }

        /************************************Project****************************************************/
        $project = Project::model()->findAll();
        $this->render('index',array(
            'sliders'=>$slider,
            'projects'=>$project,
        ));
    }
    public function actionView($alias){
        /************************************Slider****************************************************/
        $criteria = new CDbCriteria();
        $criteria->order = '`order` ASC';
        $criteria->condition = '`section` = '.Yii::app()->params['sliderItem']['project'];
        $slider = Slider::model()->findAll($criteria);
        if (!$slider){
            $criteria->condition = '`section` = '.Yii::app()->params['sliderItem']['work'];
            $slider = Slider::model()->findAll($criteria);
        }
        /*************************************Vacancy*****************************************************/
        $Project = Project::model()->find('alias=:alias',array(':alias'=>$alias));
        /***********************************SimilarVacancy*************************************************/
        if ($Project){
            $this->render('view',array(
                'sliders'=>$slider,
                'Project'=>$Project,
            ));
        }else {
            $this->render('../site/error');
        }
        //echo '<pre>';var_dump($Vacancy->attributes);exit;
    }
}