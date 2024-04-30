<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helpers\JsonResponse;
use App\Entities\FallasMtDia;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Eloquent\InternalEventRepository as Internal;
use Illuminate\Http\Request;
use App\Http\Traits\JWTTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Mail;
use Excel;
use App\Imports\IndispoInicialImport;

class SendEmailIndispoInicial implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JWTTrait;

    protected $internal;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->internal = $internal;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Request $request)
    {
        try {
            $saved = 0;
            $leads_can_be_saved = [];
            $lead_can_not_be_saved = [];
            $now = Carbon::now('America/Mexico_City');

            $file = $request->file('csv_file');
            Excel::import(new IndispoInicialImport, $file);
            DB::select("DELETE from  indispo_inicial where LENGTH(atm) >4;");
            DB::select("UPDATE indispo_inicial set atm= LPAD(atm,4,'0');");
         
            $data_for_email = [
                'id_seguimiento2'   => 'hola'
            ];

            $hola = 'Hello';

            $user_destinatario = Auth::user();

            Mail::send('emails.cargaExitosaSiga', $data_for_email, function ($m) use ($hola, $user_destinatario) {
                $m->from('marcoantonio.negrete.contractor@bbva.com', 'ESTRUCTURALES');
                $m->to($user_destinatario['email'])->subject("Se ha cargado el archivo de manera exitosa");
              });
            
            return JsonResponse::singleResponse(["message" => "Excel SIGA guardadas correctamente: " ]);

        } catch (Exception $each) {

            return JsonResponse::errorResponse("No es posible guardar las gestiones.", 404);

        }
    }
}
