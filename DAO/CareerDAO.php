<?php
    namespace DAO;

    use Models\Career as Career;
    use DAO\ICareerDAO as ICareerDAO;
    use DAO\Connection as Connection;
    use FFI\Exception;

    class CareerDAO implements ICareerDAO{

        private $careerList;
        private $nameTable;
        private $connection;
        private $fileName; 

        public function __construct()
        {
            $this->connection = Connection::GetInstance();
            $careerList = array();
            $nameTable = "career";
            $this->fileName = ROOT ."Data/careers.json";
        }

        public function GetAll() { //from DB
            $careerList = [];

            $query = "SELECT * FROM career;";

            try {
                $result = $this->connection->Execute($query);
            } catch (Exception $ex){
                throw $ex;
            }

            if(!empty($result)) { 
                foreach($result as $value) {
                    $career = new Career();

                    $career->setActive($value['active']);
                    $career->setCareerId($value['id_Career']);
                    $career->setDescription($value['description']);
            
                    array_push($careerList, $career);
                }
            }
            return $careerList;
        }

        public function existsById($id){
            $exists = null;
            $careers = $this->GetAll();

            foreach($careers as $career){
                if($career->getCareerId() == $id){
                    $exists = $career;
                }
            }
            return $exists;
        }

        private function RetrieveData() //para json
        {
            $this->careerList = array();
    
            if(file_exists($this->fileName))
            {
                $jsonContent = file_get_contents($this->fileName);
    
                $arrayToDecode = ($jsonContent) ? json_decode($jsonContent, true) : array();
    
                foreach($arrayToDecode as $valuesArray)
                {
                    $career = new Career();

                    $career->setActive($valuesArray['active']);
                    $career->setCareerId($valuesArray['id_Career']);
                    $career->setDescription($valuesArray['description']);

                    array_push($this->careerList, $career);
                }
            }
        }

        public function getCareersFromAPI(){

            $this->careerList = array();

            $apiCareer = curl_init(API_URL.'Career');
            curl_setopt($apiCareer, CURLOPT_HTTPHEADER, array(API_KEY));
            curl_setopt($apiCareer, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($apiCareer);

            $arrayToDecode = json_decode($response, true);

            foreach($arrayToDecode as $valuesArray){
                $career = new Career();

                $career->setActive($valuesArray['active']);
                $career->setCareerId($valuesArray['careerId']);
                $career->setDescription($valuesArray['description']);

                array_push($this->careerList, $career);
            }
        }
    
        public function updateCareer(Career $career){
            $sql = "UPDATE career SET description = :description , active = :active WHERE (id_Career = :id_Career);" ;
            
            $parameters['id_Career'] = $career->getCareerId();
            $parameters['description'] = $career->getDescription();
            $parameters['active'] = $career->getActive();
            
            try {
                $result = $this->connection->ExecuteNonQuery($sql, $parameters);
            } catch (Exception $ex) {
                throw $ex;
            }
            return $result;
        }

        public function updateCareerDB(){
            //$this->getCareersFromAPI();
            $this->RetrieveData();
            
            foreach ($this->careerList as $career) {
                
                $exists= $this->existsById($career->getCareerId()); //funcion que se fije si ya existe el id de la carrera en la bdd
                if($exists == null) {
                    $result = $this->AddCareerToDB($career);
                }
                else{
                    $result = $this->updateCareer($career);
                }
            }
            return $result;//si retorna 0 se agregaron todas las carreras con exito
        }

        public function AddCareerToDB(Career $career){
            $query = " INSERT INTO career (id_Career , description , active) value (:id_Career , :description , :active)";
        
            $parameters['id_Career'] = $career->getCareerId();
            $parameters['description'] = $career->getDescription();
            $parameters['active'] = $career->getActive();

            try {
                $result = $this->connection->ExecuteNonQuery($query, $parameters);

            } catch (Exception $ex) {
                throw $ex;
            }
            return $result;
        }
    
        public function GetCareerXid($idCareer){
            $query = " SELECT * FROM career WHERE id_Career = (:idCareer)";
            
            $parameters['idCareer'] = $idCareer;
            
            try {
                $result = $this->connection->Execute($query, $parameters);

            } catch (Exception $ex) {
                throw $ex;
            }

            $career = null;

            if(!empty($result)){

                foreach($result as $value){

                    $career = new Career();

                    $career->setCareerId($value['id_Career']);
                    $career->setDescription($value['description']); 
                    $career->setActive($value['active']); 
                }
            }
            return $career;
        }

        public function GetAllByCareerId($idCareer) { //from DB
            $careerList = [];

            $query = "SELECT * FROM career;";

            try {
                $result = $this->connection->Execute($query);
            } catch (Exception $ex){
                throw $ex;
            }

            if(!empty($result)) { 
                foreach($result as $value) {
                    
                    $career = new Career();

                    if($career->getCareerId() == $idCareer){
                        $career->setActive($value['active']);
                        $career->setCareerId($value['id_Career']);
                        $career->setDescription($value['description']);
                
                        array_push($careerList, $career);
                    }
                }
            }
            return $careerList;
        }

    }

?> 