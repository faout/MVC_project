<?php

    class Waterbottle
    {
        private $name;
        private $price; //per litre
        private $picture; // photo de la bouteille - url
        private $composition; // minéraux contenu dans une bouteille
        private $category; // eau de source, eau minérale, eau gazeuse etc...
        private $creator; // login
        private $creationDate;
        private $lastUpdateDate;

        // do not change, used as field name in DB
        const NAME_REF = 'name';
        const PRICE_REF = 'price';
        const PICTURE_REF = 'picture';
        const COMPOSITION_REF = 'composition';
        const CATEGORY_REF = 'category';
        const CREATOR_REF = 'creator';
        const CREATION_DATE_REF = 'creationDate';
        const LAST_UPDATE_DATE_REF = 'lastUpdateDate';

        // category options
        const SOURCE_REF = 'Eau de source';
        const GAZEUSE_REF = 'Eau gazeuse';
        const MINERALE_REF = 'Eau minérale';

        public function __construct(string $name, float $price, string $category, string $picture, string $composition, $creator, $creationDate, $lastUpdateDate=null){
            $this->name = $name;
            $this->price = $price;
            $this->category = $category;
            $this->picture = $picture;
            $this->composition = $composition;
            $this->creator = $creator;
            $this->creationDate = $creationDate;
            $this->lastUpdateDate = $lastUpdateDate;
        }

        public function getPrice() : float{
            return $this->price;
        }

        public function setPrice(float $price){
            $this->price = $price;
        }

        public function getName() : string{
            return $this->name;
        }

        public function setName(string $name){
            $this->name = $name;
        }

        public function getURLPicture() : string
        {
            return $this->picture;
        }

        public function getWaterComposition() : string // array - Faire une liste des minéraux d'une bouteille
        {
            return $this->composition;
        }

        public function setWaterComposition(string $composition){
            $this->composition = $composition;
        }

        public function getWaterCategory(): string
        {
            return $this->category;
        }

        public function setWaterCategory(string $category){
            $this->category = $category;
        }

        public function getCreator(){
            return $this->creator;
        }

        public function getCreationDate(){
            return $this->creationDate;
        }

        public function getLastUpdateDate(){
            return $this->lastUpdateDate;
        }

        public function setLastUpdateDate($date){
            $this->lastUpdateDate = $date;
        }

        public function toArray() : array{
            return array(
                Waterbottle::NAME_REF => $this->name,
                Waterbottle::PRICE_REF => $this->price,
                Waterbottle::CATEGORY_REF => $this->category,
                Waterbottle::PICTURE_REF => $this->picture,
                Waterbottle::COMPOSITION_REF => $this->composition,
                Waterbottle::CREATOR_REF => $this->creator,
                Waterbottle::CREATION_DATE_REF => $this->creationDate,
                Waterbottle::LAST_UPDATE_DATE_REF => $this->lastUpdateDate
            );
        }

        public static function fromArray(array &$attributes){
            return new Waterbottle(
                $attributes[Waterbottle::NAME_REF],
                $attributes[Waterbottle::PRICE_REF],
                $attributes[Waterbottle::CATEGORY_REF],
                $attributes[Waterbottle::PICTURE_REF],
                $attributes[Waterbottle::COMPOSITION_REF],
                $attributes[Waterbottle::CREATOR_REF],
                $attributes[Waterbottle::CREATION_DATE_REF],
                $attributes[Waterbottle::LAST_UPDATE_DATE_REF]);
        }
    }
