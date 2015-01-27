<div class="up__wrapper" path="<?php echo Yii::app()->request->baseUrl?>">
    <div class="up">
        <div id="up-slider" class="up__slider">
            <ul class="bjqs">
                <?php
                foreach ($sliders as $slider) {
                    //var_dump($slider->attributes);exit;
                    if ($slider->section == Yii::app()->params['sliderItem']['work'])
                        $sectionType = 'work_slider';
                    elseif($slider->section == Yii::app()->params['sliderItem']['project'])
                        $sectionType = 'job_slider';
                    ?>
                    <li>
                        <img src="<?php echo Yii::app()->createUrl('getFile/getImg',array('file'=>$slider->path_image,'type'=>$sectionType)) ?>"
                             title="<h1 class='bjqs-caption'><?php echo $slider->title ?></h1><span class='bjqs-caption'><?php echo $slider->description ?></span>"
                             alt="">
                    </li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>
</div>
<div class="projects__wrapper">
    <div class="block__title">
        <h1 class="">Наши проекты</h1>
    </div>
    <div class="projects">
        <?php
        if ($projects){
            foreach($projects as $project){
                //var_dump($vacancy);exit;
                ?>
                <div class="projects__item__wrapper inline-block">
                    <div class="projects__item">
                        <div class="ProjectLogo">
                            <?php if (!empty($project['path_image'])){
                                echo Project::ShowImg('title',$project['path_image'],"398");
                            }?>
                        </div>
                        <?php echo CHtml::link($project['name'],array('/project/view','alias'=>$project['alias']),array('class'=>'projects__item__title'))?>
                        <?php echo CHtml::tag('span',array('class'=>'projects__item__status'),Yii::app()->params['projectStatus'][$project['status']],true);?>
                        
                        <p class="projects__item__text">
                            <?php echo strip_tags($project['anons'])?>
                        </p>
                    </div>
                </div>
            <?php
            }
        }
        else echo '<i>Нет проектов</i>';
        ?>
    </div>
</div>
