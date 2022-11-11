<?php

use App\Models\History;
use App\Models\Service;

class HistoryTest extends TestCase{
    
    // create
    public function testShouldCreateHistory(){
        $data = Service::orderByDesc('id')->first();
        $params = [
            'status'=>'perbaikan selesai',
            'pesan'=>'perbaikan selesai cuy'
        ];
        $this->post('/services/'.$data->id.'/history',$params,['Authorization'=>'Bearer '.$this->teknisi()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idRiwayat'
            ]
            ]);
        $history = History::where('idService',$data->id)->orderByDesc('id')->first();
        History::where('id',$history->id)->delete();
    }
}

?>