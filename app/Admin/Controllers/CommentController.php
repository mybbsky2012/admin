<?php

namespace App\Admin\Controllers;

use App\Models\Article;
use App\Models\Blog;
use App\Models\Comment;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Str;

class CommentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '评论管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Comment);
        $grid->model()->orderBy('id', 'desc');

        $grid->quickSearch('email', 'name', 'link', 'content');
        $grid->filter(function($filter) {
            $filter->disableIdFilter();
            $filter->like('name', __('博客名称'));
            $filter->like('email', __('邮箱'));
            $filter->like('link', __('链接'));
            $filter->like('content', __('内容'));
        });

        $grid->column('id', __('ID'));
//        $grid->column('parent_id', __('上级评论'));
        $grid->column('foreign_id')->display(function ($foreignId, $column) {
            if ('article' === $this->type) {
                $article = Article::find($foreignId);
                return '文章: ' . ($article ? $article->title : '-');
            }

            if ('blog' === $this->type) {
                $blog = Blog::find($foreignId);
                return '博客: ' . ($blog ? $blog->name : '-');
            }
        });
        $grid->column('type', __('类型'))->using(Comment::$types)
            ->filter(Comment::$types)
            ->label();
        $grid->column('email', __('邮箱'));
        $grid->column('name', __('名称'));
        $grid->column('link', __('链接'))
            ->link();
        $grid->column('content', __('评论内容'))->display(function ($content) {
            return Str::limit($content, 100);
        });
        $grid->column('status', __('状态'))
            ->using(Comment::$status)
            ->filter(Comment::$status)
            ->label();
        $grid->column('created_at', __('评论时间'));
        $grid->disableCreateButton();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Comment::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('type', __('类型'));
        $show->field('email', __('邮箱'));
        $show->field('name', __('名称'));
        $show->field('link', __('链接'));
        $show->field('content', __('评论内容'));
        $show->field('status', __('状态'));
        $show->field('created_at', __('评论时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Comment);

        $form->select('type', __('类型'))->options(Comment::$types)->disable();
        $form->email('email', __('邮箱'))->rules('required|email');
        $form->text('name', __('名称'))->rules('required|max:30');
        $form->url('link', __('链接'))->rules('required|url');
        $form->textarea('content', __('评论内容'));
        $form->radio('status', __('状态'))
            ->options(Comment::$status)
            ->default(1);

        return $form;
    }
}