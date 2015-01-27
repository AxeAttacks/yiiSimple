<?php
/**
 * Created by PhpStorm.
 * User: mvnosulenko
 * Date: 05.11.14
 * Time: 16:26
 */
class Project extends CActiveRecord{
    public $image;
    public $id_category;
    public $id_worker;
    public $id_vacancy;
    public $old_alias;

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{project}}';
    }

    public function primaryKey()
    {
        return 'id';
    }
    public function rules()
    {
        return array(
            array('name, description, anons, order, id_category', 'required'),
            array('image', 'required', 'on'=>'create'),
            array('anons', 'length','max'=>200),
            array('image', 'file', 'types'=>'bmp, jpg, gif, png','allowEmpty'=>true),
            array('image','getWidthImage'),
            array('status, id_worker, id_vacancy', 'safe'),
            array('alias','ext.LocoTranslitFilter','translitAttribute'=>'name'),
        );
    }
    public function orderScope($params){
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>"`order`= ".(int)$params['order'],
        ));
        return $this;
    }
    public function getWidthImage($attribute){
        if ($this->image){
            $temp = getimagesize($this->image->getTempName());
            if ($temp[0] != 398){
                $this->addError($attribute,'Ширина изображения должна быть 398px');
            }
        }
    }
    public function relations()
    {
        return array(
            'categories'=>array(self::MANY_MANY, 'ProjectCategory',
                'vac_project_category(id_project, id_category)'),
            'vacancies'=>array(self::MANY_MANY, 'Vacancy',
                'vac_project_vacancy(id_project, id_vacancy)'),
            'workers'=>array(self::MANY_MANY, 'Worker',
                'vac_project_worker(id_project, id_worker)'),
            'attachFiles'=>array(self::HAS_MANY, 'ProjectFiles',
                'id_project'),
        );
    }
    public function attributeLabels()
    {
        return array(
            'name' => 'Наименование',
            'image' => 'Аватара',
            'description' => 'Описание',
            'anons' => 'Анонс',
            'path_image' => 'Путь к файлу',
            'id_category' => 'Категория',
            'id_worker' => 'Сотрудники',
            'id_vacancy' => 'Вакансии',
            //'id_year' => 'Год',
        );
    }
    public function ShowImg($title, $image, $width='128')
    {
        if(isset($image) && !empty($image) /*&& file_exists($_SERVER['DOCUMENT_ROOT'].
                Yii::app()->request->baseUrl.'/img/rest/holiday/slider/'.$image)*/)
            return CHtml::image(Yii::app()->createUrl('getFile/getImg',array('file'=>$image,'type'=>'project')), $title,
                array(
                    'width'=>$width,
                )
            );
    }
    public function afterSave(){
        $_GET['type'] = 'project';
        $_GET['fileUploadDir'] = '';
        require_once Yii::getPathOfAlias('application.config.directory').'.php';
        $path = $DirectoryConfig['directory'];
        if ($this->image){
            $this->image->saveAs($path . $this->path_image);
        }
        if (isset($this->id_category)){
            $sql = "DELETE FROM vac_project_category WHERE `id_project` = ".$this->id;
            $command = Yii::app()->db->createCommand($sql);
            $command->execute();
            foreach($this->id_category as $category){
                $sql="INSERT INTO vac_project_category(id_project, id_category) VALUES(".$this->id.",'".$category."')";
                $command = Yii::app()->db->createCommand($sql);
                $command->execute();
            }
        }
        if (isset($this->id_worker)){
            $sql = "DELETE FROM vac_project_worker WHERE `id_project` = ".$this->id;
            $command = Yii::app()->db->createCommand($sql);
            $command->execute();
            foreach($this->id_worker as $worker){
                $sql="INSERT INTO vac_project_worker(id_project, id_worker) VALUES(".$this->id.",'".$worker."')";
                $command = Yii::app()->db->createCommand($sql);
                $command->execute();
            }
        }
        if (isset($this->id_vacancy)){
            $sql = "DELETE FROM vac_project_vacancy WHERE `id_project` = ".$this->id;
            $command = Yii::app()->db->createCommand($sql);
            $command->execute();
            foreach($this->id_vacancy as $vac){
                $sql="INSERT INTO vac_project_vacancy(id_project, id_vacancy) VALUES(".$this->id.",'".$vac."')";
                $command = Yii::app()->db->createCommand($sql);
                $command->execute();
            }
        }
        $i = 0;
        if (isset($_POST['ProjectFiles'])){
            if ((!file_exists($path . $this->alias))&&(!$this->old_alias)){
                mkdir($path.$this->alias, 0777);
            }
            if(($this->old_alias)&&(file_exists($path . $this->old_alias))){
                rename($path.$this->old_alias, $path.$this->alias);
            }
            foreach($_POST['ProjectFiles']['name'] as $row){
                //var_dump($row);exit;
                $Files = new ProjectFiles('create');
                $Files->name = (empty($row)) ? 'Без названия' : $row;
                $Files->image = CUploadedFile::getInstance($Files, 'image['.$i.']');
                if ($Files->image){
                    $imageName = time().'_'.rand(10000,99900).'.'.$Files->image->getExtensionName();
                    $Files->file = $imageName;
                }
                $Files->id_project = $this->id;
                if ($Files->save()){
                    if ($Files->image){
                        $Files->image->saveAs($path . $this->alias. '/' . $imageName);
                    }
                }
                $i++;
            }
        }
    }
}