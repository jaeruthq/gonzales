<?php

namespace torremall;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
class DatosUsuario extends Model
{
    protected $fillable = [
        'nom','apep','apem','ci',
        'ci_exp','dir','email','fono',
        'cel','foto','user_id'
    ];

    /* =================================================== 
                            RELACIONES
    ====================================================== */
    public function user()
    {
        return $this->belongsTo('torremall\User','user_id','id');
    }

    /*============================================================== 
                        FUNCIONES
      ==============================================================*/
    
    public static function lista()
    {
        return DB::select("SELECT du.id as datos_id, CONCAT(du.nom,' ',du.apep,' ',du.apem) AS nombre, CONCAT(du.ci,' ',du.ci_exp) AS ci, du.fono, du.cel, du.foto, u.tipo,u.name as codigo, u.id as user_id 
                        FROM datos_usuarios du
                        INNER JOIN  users u on u.id = du.user_id
                        WHERE u.status = 1");
    }
}
