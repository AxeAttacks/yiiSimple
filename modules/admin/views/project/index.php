<?php
/**
 * Created by PhpStorm.
 * User: mvnosulenko
 * Date: 10.11.14
 * Time: 17:41
 */
?>
<nav class="breadcrumbs" >
    <ul>
        <li><a href="<?php echo Yii::app()->createUrl('admin')?>"><i class="icon-home"></i></a></li>
        <li><?php echo CHtml::link('Проекты',Yii::app()->createUrl('admin/page', array('id'=>Yii::app()->params['item']['project'])))?></li>
        <li class="active"><a href="#">Управление проектами</a></li>
    </ul>
</nav>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'project-grid',
    'dataProvider'=>$model,
    'summaryText'=>'',
    //'htmlOptions' => array('class' => 'table bordered'),
    'itemsCssClass' => 'table bordered',
    //'rowCssClassExpression' => '(empty($data["parent_id"])) ? "odd" : "even"',
    'pager' => array('header' => ''),
    'columns'=>array(
        array(
            'name' => 'name',
            'header' => 'Заголовок',
            'type' => 'raw',
        ),
        array(
            'name' => 'description',
            'header' => 'Описание',
            'value' => 'strip_tags($data->description)',
            'type' => 'raw',
        ),
        array(
            'name' => 'anons',
            'header' => 'Анонс',
            'type' => 'raw',
        ),
        array(
            'name' => 'path_image',
            'header' => 'Изображение',
            'value' => 'Project::ShowImg("title",$data->path_image,"100")',
            'type' => 'raw',
        ),
        array(
            'class'=>'CButtonColumn',
            'template'=>'{update}{delete}{up}{down}',
            'buttons'=>array(
                'update'=>array(
                    'url'=>'$this->grid->controller->createUrl("project/update/",array("id"=>$data->primaryKey))',
                ),
                'delete'=>array(
                    'url'=>'$this->grid->controller->createUrl("project/delete/",array("id"=>$data->primaryKey))',
                    'options' => array(
                        'ajax' => array(
                            'type' => 'get',
                            'url'=>'js:$(this).attr("href")',
                            'success' => 'js:function(data) {
                                    window.location.reload();
                                    //$.fn.yiiGridView.update("section-grid")
                                    }'
                        )
                    ),
                ),
                'up'=>array(
                    'label'=>'Поднять наверх',
                    'imageUrl'=>Yii::app()->request->baseUrl.'/img/up.gif',
                    'url'=>'Yii::app()->createUrl("order/orderUp/",array("params" => array("modelName"=>"Project","order"=>$data->order)))',
                    'options' => array(
                        'ajax' => array(
                            'type' => 'get',
                            'url'=>'js:$(this).attr("href")',
                            'success' => 'js:function(data) {
                                $.fn.yiiGridView.update("project-grid");
                            }'
                        )
                    ),
                ),
                'down'=>array(
                    'label'=>'Опустить вниз',
                    'imageUrl'=>Yii::app()->request->baseUrl.'/img/down.gif',
                    'url'=>'Yii::app()->createUrl("order/orderDown/",array("params" => array("modelName"=>"Project","order"=>$data->order)))',
                    'options' => array(
                        'ajax' => array(
                            'type' => 'get',
                            'url'=>'js:$(this).attr("href")',
                            'success' => 'js:function(data) {
                                $.fn.yiiGridView.update("project-grid");
                            }'
                        )
                    ),
                ),
                /*'up'=>array(
                    'label'=>'Поднять наверх',
                    'imageUrl'=>Yii::app()->request->baseUrl.'/img/up.gif',
                    'url'=>'Yii::app()->createUrl("admin/project/orderUp/",array("order"=>$data->order))',
                    'options' => array(
                        'ajax' => array(
                            'type' => 'get',
                            'url'=>'js:$(this).attr("href")',
                            'success' => 'js:function(data) {
                                $.fn.yiiGridView.update("project-grid");
                            }'
                        )
                    ),
                ),
                'down'=>array(
                    'label'=>'Опустить вниз',
                    'imageUrl'=>Yii::app()->request->baseUrl.'/img/down.gif',
                    'url'=>'Yii::app()->createUrl("admin/project/orderDown/",array("order"=>$data->order))',
                    'options' => array(
                        'ajax' => array(
                            'type' => 'get',
                            'url'=>'js:$(this).attr("href")',
                            'success' => 'js:function(data) {
                                $.fn.yiiGridView.update("project-grid");
                            }'
                        )
                    ),
                ),*/
            ),
        ),
    ),
));
$this->widget('bootstrap.widgets.TbButton',
    array(
        'label'=>'Добавить',
        'htmlOptions'=>array(
            'class' => 'button default',
            'style'=>"background-color: #1ba1e2;padding-left: 12px;"
        ),
        'url'=>array('project/create'),
    )
);