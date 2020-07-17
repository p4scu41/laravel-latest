<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $activityLogCls = config('activitylog.activity_model');
        $response = QueryBuilder::for($activityLogCls)
            ->defaultSort('-created_at')
            ->allowedFilters([
                'log_name',
                'description',
                AllowedFilter::exact('subject_id'),
                AllowedFilter::exact('subject_type'),
                AllowedFilter::exact('causer_id'),
                AllowedFilter::exact('causer_type'),
            ])
            ->allowedSorts([
                'log_name',
                'description',
                'subject_id',
                'subject_type',
                'causer_id',
                'causer_type',
            ])
            ->paginate()
            ->appends($request->query())
            ->toArray();

        $response['options'] = [
            'log_name'     => $this->getOptions('log_name'),
            'description'  => $this->getOptions('description'),
            'subject_type' => $this->getOptions('subject_type'),
            'causer_type'  => $this->getOptions('causer_type'),
        ];

        return $response;
    }

    /**
     * @param string $column
     *
     * @return array
     */
    private function getOptions($column)
    {
        $activityLogCls = config('activitylog.activity_model');
        $options = $activityLogCls::select($column)
            ->groupBy($column)
            ->get()
            ->pluck($column)
            ->toArray();

        return array_values(array_filter($options, 'strlen'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $activityLogCls = config('activitylog.activity_model');

        return $activityLogCls::find($id);
    }
}
