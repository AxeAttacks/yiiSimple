<?php
/**
 * Created by PhpStorm.
 * User: mvnosulenko
 * Date: 05.11.14
 * Time: 16:25
 */
class ProjectController extends CController
{
    public $layout='//layouts/admin';
    public $Category = array();
    public $Worker = array();
    public $Job = array();
    public function filters(){
        return array(
            'accessControl',
        );
    }

    public function accessRules(){
        return array(
            //запускать действие index может только пользователь с ролью admin
            array('allow',
                //'actions'=>array('index'),
                'roles'=>array('admin'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public function actionIndex(){
        $criteria = new CDbCriteria();
        $criteria->order = '`order` ASC';
        $model = Project::model()->findAll($criteria);
        $dp = new CArrayDataProvider($model,array(
            'pagination' => array(
                'pageSize' => 10,
                //'itemCount' => 80,
            ),
        ));
        $this->render('index',array(
            'model'=>$dp,
        ));
    }
    public function actionCreate(){
        $model = new Project();
        $this->getDataArray();
        $Files = new ProjectFiles('create');
        if(isset($_POST['ajax']) && $_POST['ajax']==='createProject-form'){
            echo CActiveForm::validate($model);
            echo CActiveForm::validate($Files);
            Yii::app()->end();
        }
        if(isset($_POST['Project'])){
            $model->attributes = $_POST['Project'];
            $count = Yii::app()->db->createCommand()
                ->select('COUNT(*) as count')
                ->from('vac_project p')
                ->queryRow();
            $model->order = $count['count'] + 1;
            $model->image = CUploadedFile::getInstance($model, 'image');
            if ($model->image){
                $fileName = time().'_'.rand(10000,99900).'.'.$model->image->getExtensionName();
                $model->path_image = $fileName;
            }
            if (isset($model->status[0])) $model->status = $model->status[0];
            if ($model->save()){
                /********************************************************************************/
                $this->redirect(array('index'));
            }
        }
        $this->render('_form',array('model'=>$model,'Category'=>$this->Category,'Worker'=>$this->Worker, 'Job'=>$this->Job, 'Files'=>$Files));
    }
    public function actionUpdate($id){
        $model = Project::model()->findByPk($id);
        $this->getDataArray();
        $this->getDataByForeignKey($model);
        if (!empty($model->attachFiles)){
            foreach($model->attachFiles as $attachFiles){
                $Files[] = $attachFiles;
            }
        }
        else{
            $Files = new ProjectFiles();
        }
        if(isset($_POST['ajax']) && $_POST['ajax']==='updateProject-form'){
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        if(isset($_POST['Project'])){
            $model->old_alias = $model->alias;
            $model->attributes = $_POST['Project'];
            $model->image = CUploadedFile::getInstance($model, 'image');
            if ($model->image){
                $imageName = time().'_'.rand(10000,99900).'.'.$model->image->getExtensionName();
                $model->path_image = $imageName;
            }
            if (isset($model->status[0]))
                $model->status = $model->status[0];
            if ($model->save()){
                $this->redirect(array('index'));
            }
        }
        $this->render('_form',array('model'=>$model,'Category'=>$this->Category,'Worker'=>$this->Worker, 'Job'=>$this->Job, 'Files'=>$Files));
    }
    public function getDataArray(){
        $criteria = new CDbCriteria();
        $criteria->order = '`name` ASC';
        $Categories = ProjectCategory::model()->findAll($criteria);
        foreach ($Categories as $row){
            $this->Category[$row->id] = $row->name;
        }
        $Workers = Worker::model()->findAll($criteria);
        foreach ($Workers as $row){
            $this->Worker[$row->id] = $row->name.', '.$row->post;
        }
        $criteria = new CDbCriteria();
        $criteria->order = '`title` ASC';
        $criteria->condition = '`isPublic` = 1';
        $Jobs = Vacancy::model()->findAll($criteria);
        foreach ($Jobs as $row){
            $this->Job[$row->id] = $row->title;
        }
        //echo '<pre>';var_dump($this);exit;
    }
    public function getDataByForeignKey($model){
        if (!empty($model->categories)){
            foreach($model->categories as $category){
                $model->id_category[] = $category->id;
            }
        }
        if (!empty($model->vacancies)){
            foreach($model->vacancies as $vac){
                $model->id_vacancy[] = $vac->id;
            }
        }
        if (!empty($model->workers)){
            foreach($model->workers as $worker){
                $model->id_worker[] = $worker->id;
            }
        }
        return $model;
    }
    public function actionDelete($id){
        $model = Project::model()->findByPk($id);
        $_GET['type'] = 'project';
        require_once Yii::getPathOfAlias('application.config.directory').'.php';
        $path = $DirectoryConfig['directory'];
        if ($model->delete()){
            /*if (!empty($model->path_image)){
                unlink($path.$model->path_image);
            }*/
        }
    }
    public function actionDeleteFileProject($id){
        $model = ProjectFiles::model()->findByPk($id);
        if ($model->delete()){
            $this->redirect(array('/admin/project/update','id'=>$model->id_project));
        }
    }
}