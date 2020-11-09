<?php



class TimeTableForm {

    public $values;
    public $timetable;
    public function __construct(){

            $this->timetable=array(
 'Mon'=>array('title'=>"Poniedziałek")
,'Tue'=>array('title'=>"Wtorek")
,'Wed'=>array('title'=>"Środa")
,'Thu'=>array('title'=>"Czwartek")
,'Fri'=>array('title'=>"Piątek")
,'Sat'=>array('title'=>"Sobota")
,'Sun'=>array('title'=>"Niedziela")
);

            $this->values=array(
                'Mon'=>array('start1'=>'00:00','stop1'=>'00:00','start2'=>'00:00','stop2'=>'00:00'),
                'Tue'=>array('start1'=>'00:00','stop1'=>'00:00','start2'=>'00:00','stop2'=>'00:00'),
                'Wed'=>array('start1'=>'00:00','stop1'=>'00:00','start2'=>'00:00','stop2'=>'00:00'),
                'Thu'=>array('start1'=>'00:00','stop1'=>'00:00','start2'=>'00:00','stop2'=>'00:00'),
                'Fri'=>array('start1'=>'00:00','stop1'=>'00:00','start2'=>'00:00','stop2'=>'00:00'),
                'Sat'=>array('start1'=>'00:00','stop1'=>'00:00','start2'=>'00:00','stop2'=>'00:00'),
                'Sun'=>array('start1'=>'00:00','stop1'=>'00:00','start2'=>'00:00','stop2'=>'00:00'),
            );

                                }

    public function generate(){


        $context=Context::getContext();
        $context->smarty->assign('time_table',$this->timetable);
        $context->smarty->assign('values',$this->values);


        $template='
{foreach from=$time_table key=k item=v}
<div class="row">
    <div class="col-md-2">
      <label> {$v.title}</label>
    
    </div>
    
    <div class="col-md-2">
       <label for="TimeTable{$k}start1"> Start 1</label>

      <input type="time" id="TimeTable{$k}start1"  name="TimeTable[{$k}][start1]" value="{$values.$k.start1}" />
    </div>
    
    <div class="col-md-2">
     <label for="TimeTable{$k}stop1"> Stop 1</label>
      <input type="time" id="TimeTable{$k}stop1" name="TimeTable[{$k}][stop1]" value="{$values.$k.stop1}" />
      
    </div>
    <div class="col-md-2">
     <label for="TimeTable{$k}start2"> Start 2</label>
      <input type="time" id="TimeTable{$k}start2" name="TimeTable[{$k}][start2]" value="{$values.$k.start2}"  />
    </div>
    <div class="col-md-2">
        <label for="TimeTable{$k}stop2"> Stop 2</label>
      <input type="time" id="TimeTable{$k}stop2" name="TimeTable[{$k}][stop2]" value="{$values.$k.stop2}" />
      
    </div>

  </div>
  {/foreach}
  ';

      return  $context->smarty->fetch('string:'.$template);

    }


}