<?php

namespace App\Admin\Forms;

use App\Config;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class Setting extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '系统设置';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        $configs = $request->all();
        foreach ($configs as $key => $val) {
            if (in_array($key, [
                'review_comment', 'close_apply', 'auto_detection', 'auto_writing_dateline'
            ])) {
                $val = $val === 'on' ? 1 : 0;
            }

            Config::where('key', $key)->update(['value' => $val]);
        }

        admin_success('更新成功');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $states = [
            'on'  => ['value' => 1, 'text' => '是', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '否', 'color' => 'danger'],
        ];

        $this->switch('review_comment', __('评论是否需要审核'))->states($states);
        $this->switch('close_apply', __('是否关闭申请通道'))->states($states);
        $this->switch('auto_detection', __('是否开启自动检测'))->states($states)
            ->help('是否开启自动检测博客状态');
        $this->number('auto_writing_period', __('自动检测周期'))
            ->help('以小时为单位');
        $this->number('max_abnormal_num', __('最大异常次数'))
            ->help('博客当天最大异常次数, 超出后将列入疑似异常名单');
        /*$this->switch('auto_writing_dateline', __('异常自动写入大事记'))
            ->states($states)
            ->help('自动检测到博客当天异常数量超出后是否自动写入大事记');*/
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        $configs = Config::all()->whereIn('key', [
            'review_comment', 'close_apply', 'auto_detection', 'auto_writing_dateline', 'auto_writing_period', 'max_abnormal_num'
        ]);
        $data = [];
        foreach ($configs as $config) {
            $data[$config->key] = $config->value;
        }

        return $data;
    }
}
