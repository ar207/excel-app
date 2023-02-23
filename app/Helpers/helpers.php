<?php

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

/**
 * This is used to get current date time
 *
 * @return string
 */
function currentDateTime()
{
    $time = \Carbon\Carbon::now();

    return $time->toDateTimeString();
}

/**
 * This is used to get current date time
 *
 * @return string
 */
function currentDate()
{
    return date_format(new \DateTime(), 'd-m-Y');
}

/**
 * make_complete_pagination_block
 * @param $obj
 * @param string $type | three possible values 1)short (for short paragraph) 2)long (for long paragraph) 3) null (for no paragraph) .
 * @return  complete pagination block
 */
function getPagination($data, $total)
{
    $info = get_pager_info_paragraph($data, true, $total, 'long');

    return view('partials._pager', compact('data', 'info'))->render();
}

/**
 * Used to paginate array data
 *
 * @param $items
 * @param int $perPage
 * @param null $page
 * @param array $options
 * @return LengthAwarePaginator
 */
function paginateArrayData($items, $perPage, $page = null, $options = [])
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);

    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
}


/**
 * make_complete_pagination_block
 * @param $obj
 * @param string $type | three possible values 1)short (for short paragraph) 2)long (for long paragraph) 3) null (for no paragraph) .
 * @return  complete pagination block
 */
function make_complete_pagination_block($obj, $totalRecord, $type = null, $is_simple = null)
{
    $info = get_pager_info_paragraph($obj, $is_simple, $totalRecord, $type);

    return view('partials._pager', compact('info', 'obj'))->render();
}

/**
 * get_pager_info_paragraph | it will a paginator object provided by laravel paginate method and will return a paragraph line item with the info about total records and showing records range according to the current page.
 * @param array $obj | paginator object provided by laravel paginate method
 * @param string $type | three possible values 1)short (for short paragraph) 2)long (for long paragraph) 3) null (for no paragraph) .
 * @return returns string | returns a string (paragraph line with star end and total records according to the current page.)
 *
 */
function get_pager_info_paragraph($obj, $is_simple, $totalRecord, $type = null)
{
    $info = "";
    $end = $obj->currentPage() * $obj->perPage();
    $start = $end - ($obj->perPage() - 1);
    $current_page = $obj->currentPage();
    $total = $totalRecord;
    if (!empty($is_simple)) {
        $last_page = ($total - 1) * $obj->perPage();
    } else {
        $last_page = $obj->lastPage();
    }
    if ($start < 1) {
        $start = 1;
    }
    if ($end > $total) {
        $end = $total;
    }
    $type = 'long';
    if ($type) {
        if ($total > 0) {
            if ($type == 'long') {
                $info = "<div class='pager-info'><p>$start to $end from $total results.</p><div class='clr'></div></div>";
            } else {
                $info = "<div class='pager-info'><p>Side $current_page of $last_page </p><div class='clr'></div></div>";
            }
        }
    }

    return $info;
}

/**
 * This function returns login user id
 *
 * @return mixed
 */
function loginId()
{
    $id = 0;
    if (\Auth::check())
        $id = \Auth::user()->id;

    return $id;
}

/**
 * This function returns login user name
 *
 * @return mixed
 */
function loginUserName()
{
    $name = '';
    if (\Auth::check()) {
        $name = \Auth::user()->name;
    }

    return $name;
}

/**
 * This function returns login user email
 *
 * @return mixed
 */
function loginUserEmail()
{
    $name = '';
    if (\Auth::check())
        $name = \Auth::user()->email;

    return $name;
}

/**
 * This is used to correct the ndc syntax
 *
 * @param $val
 * @return int|string
 */
function ndcCorrection($val)
{
    $ndc = '';
    if (!empty($val)) {
        $ndcValue = str_replace('-', '', $val);
        $ndcLength = strlen($ndcValue);
        $remainingNdc = 11 - $ndcLength;
        $zero = 0;
        if (!empty($remainingNdc)) {
            for ($i = 1; $i <= $remainingNdc; $i++) {
                $ndcValue = $zero . $ndcValue;
            }
        }
        $firstNdc = substr($ndcValue, 0, 5);
        $secondNdc = substr($ndcValue, 5, 4);
        $thirdNdc = substr($ndcValue, 9, 2);
        $ndc = $firstNdc . '-' . $secondNdc . '-' . $thirdNdc;
    }

    return $ndc;
}
