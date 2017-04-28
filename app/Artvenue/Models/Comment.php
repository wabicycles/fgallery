<?php
namespace App\Artvenue\Models;

/**
 * @author Abhimanyu Sharma <abhimanyusharma003@gmail.com>
 */
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{

    use SoftDeletes, SoftCascadeTrait;
    /**
     * @var string
     */
    protected $table = 'comments';
    /**
     * @var bool
     */
    protected $softDelete = true;

    /**
     * @var array
     */
    protected $softCascade = ['replies', 'votes'];
    
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return mixed
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * @return mixed
     */
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    /**
     * @return mixed
     */
    public function votes()
    {
        return $this->hasMany(CommentsVotes::class, 'comment_id');
    }
}