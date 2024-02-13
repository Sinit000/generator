
<?php
class GetWeekday{
    public function getday($date){
        return date('w', strtotime($date));
    }
}