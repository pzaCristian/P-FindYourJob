<?php
    namespace Models;

    class JobOffer {

        private $idJobOffer;
        private $company;
        private $jobPosition;
        private $description;
        private $datetime;
        private $limitDate;
        private $timeState;
        private $flyer;
        private $notified;

        public function setIdJobOffer($idJobOffer){
            $this->idJobOffer = $idJobOffer;
        }

        public function getIdJobOffer(){
            return $this->idJobOffer;
        }

        public function setCompany(Company $company){
            $this->company = $company;
        }

        public function getCompany(){
            return $this->company;
        }

        public function setJobPosition(JobPosition $jobPosition){
            $this->jobPosition = $jobPosition;
        }

        public function getJobPosition(){
            return $this->jobPosition;
        }

        public function setDescription($description){
            $this->description = $description;
        }

        public function getDescription(){
            return $this->description;
        }

        public function setDatetime($datetime){
            $this->datetime = $datetime;
        }

        public function getDatetime(){
            return $this->datetime;
        }

        public function setLimitDate($limitDate){
            $this->limitDate = $limitDate;
        }

        public function getLimitDate(){
            return $this->limitDate;
        }

        public function setTimeState ($timeState){
            $this->timeState = $timeState;
        }

        public function getTimeState(){
            return $this->timeState;
        }

        public function getFlyer(){
            return $this->flyer;
        }
    
        public function setFlyer($flyer){
            $this->flyer = $flyer;
        }

        public function getNotified(){
            return $this->notified;
        }

        public function setNotified($notified){
            $this->notified = $notified;
        }

    }


?>