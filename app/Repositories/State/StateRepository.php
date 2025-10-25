<?php

namespace App\Repositories\State;



use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

//use Your Model



/**

 * Class CategoryRepository.

 */

use App\Models\State;



class StateRepository implements StateRepositoryInterface

{

    /**

     * @return string

     *  Return the model

     */

    public function find($id)

    {

        return State::findOrFail($id)->toArray(); 

    }



    public function all()

    {

        return State::get()->toArray();

    }




    public function createOrUpdate($request,$id=null)

    {
        $data = $request->all();



            if ($id) 

            {

                $State = State::find($id);

                if ($State) {

                    $State->update($data);

                                    

                } else {

                    throw new \Exception("State with ID {$id} not found.");

                }

            } else {

                $State = State::create($data); // Create a new record

            }



            return $State;
}


public function changeStatus($request, $id)
    {
        $State = State::find($id);

        if (!$State) {

            throw new \Exception("State with ID {$id} not found.");

        }

        $State->isDelete = $request['isDelete'] ?? 0;
        $State->save();
        return $State;
    }


    public function destroy($id){

        State::where('stateId',$id)->delete();

    }

    public function query()
    {
        return State::query();
    }

}

