<?php

/*======================================================================*\
|| #################################################################### ||
|| # Rhino Socket 2.0                                                 # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright 2014 Rhino All Rights Reserved.                        # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| #   ---------------- Rhino IS NOT FREE SOFTWARE ----------------   # ||
|| #                  http://www.livesupportrhino.com                 # ||
|| #################################################################### ||
\*======================================================================*/

class Paginator{

	var $items_total;
	var $current_page;
	var $num_pages;
	var $mid_range;
	var $low;
	var $high;
	var $limit;
	var $return;
	var $items_per_page;
	var $jak_get_page;
	var $jak_where;
	var $jak_prevstyle = 'prev-button';
	var $jak_nextstyle = 'next-button';
	var $jak_prevtext = '<<';
	var $jak_nexttext = '>>';

	function Paginator()
	{
		$this->current_page = 1;
		$this->mid_range = 3;
	}

	function paginate()
	{
		$this->num_pages = ceil($this->items_total/$this->items_per_page);
		$this->current_page = (int) $this->jak_get_page; // must be numeric > 0
		if($this->current_page < 1 Or !is_numeric($this->current_page)) $this->current_page = 1;
		if($this->current_page > $this->num_pages) $this->current_page = $this->num_pages;
		$prev_page = $this->current_page-1;
		$next_page = $this->current_page+1;

		if($this->num_pages > 1) {
			
			$this->return = ($this->current_page != 1 And $this->items_total >= 2) ? ' <ul class="pagination"><li><a class="'.$this->jak_prevstyle.'" href="'.$this->jak_where.LS_rewrite::lsParseurlpaginate($prev_page).'">'.$this->jak_prevtext.'</a></li>' : '<ul class="pagination">';

			$this->start_range = $this->current_page - floor($this->mid_range/2);
			$this->end_range = $this->current_page + floor($this->mid_range/2);

			if($this->start_range <= 0)
			{
				$this->end_range += abs($this->start_range)+1;
				$this->start_range = 1;
			}
			if($this->end_range > $this->num_pages)
			{
				$this->start_range -= $this->end_range-$this->num_pages;
				$this->end_range = $this->num_pages;
			}
			$this->range = range($this->start_range,$this->end_range);
			
			for($i=1;$i<=$this->num_pages;$i++)
			{
				// loop through all pages. if first, last, or in range, display
				if($i==1 Or $i==$this->num_pages Or in_array($i,$this->range))
				{
					$this->return .= ($i == $this->current_page) ? '<li class="active"><a title="Go to page '.$i.' of '.$this->num_pages.'" href="'.$this->jak_where.LS_rewrite::lsParseurlpaginate($i).'">'.$i.'</a></li>' : '<li><a title="Go to page '.$i.' of '.$this->num_pages.'" href="'.$this->jak_where.LS_rewrite::lsParseurlpaginate($i).'">'.$i.'</a></li>';
				}
			}
			$this->return .= ($this->current_page != $this->num_pages And $this->items_total >= 2) ? '<li><a href="'.$this->jak_where.LS_rewrite::lsParseurlpaginate($next_page).'">'.$this->jak_nexttext.'</a></li></ul>' : '</ul>';
		}
		$this->low = ($this->current_page-1) * $this->items_per_page;
		$this->high = ($this->current_page * $this->items_per_page)-1;
		$this->limit = 'LIMIT '.$this->low.','.$this->items_per_page;
	}

	function display_pages()
	{
		return $this->return;
	}
}