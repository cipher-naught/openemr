<?php
class Zend_Helper_ArrayView extends Zend_View_Helper_Abstract 
{
	public function arrayView()
	{
		return $this;
	}
	
	public function makeLiList($items)
	{
		$lcl = "";
		foreach($items as $data)
		{
			$lcl .= "<li>".$data."</li>";
		}
		return $lcl;
	}
	
	public function makeDDList($items,$values)
	{
			
		
		$lcl = "<select>";
		if(count($items)== count($values)) {
			//Return error
			$i = 0;
			while($i < count($items))
			{
				$lcl .= "<option value=".$values[$i].">".$items[$i]."</option>\n";
				$i = $i+1;
			}
			return $lcl."</select>\n";
		}
		else {
			//stuff
			//TODO: Raise error
		}
		
	}
	
	/*public function makeDDList($items)
	{
		return makeDDList($items,$items);
	}*/
	
}