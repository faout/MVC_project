<?php
    
    class Comment{

        private $writer; // login
        private $waterbottle; // id
        private $writingDate;
        private $comment;

        // do not change, used as field name in DB
        const WRITER_REF = 'writer';
        const WATERBOTTLE_REF = 'waterbottle';
        const WRITING_DATE_REF = 'writingDate';
        const COMMENT_REF = 'comment';

        public function __construct($writer, $waterbottle, $writingDate, $comment){
            $this->writer = $writer;
            $this->waterbottle = $waterbottle;
            $this->writingDate = $writingDate;
            $this->comment = $comment;
        }

        public function getWriterLogin(){
            return $this->writer;
        }

        public function getWaterbottleID(){
            return $this->waterbottle;
        }

        public function getWritingDate(){
            return $this->writingDate;
        }

        public function getText(){
            return $this->comment;
        }

        public static function fromArray(array &$attributes) : Comment{
            return new Comment(
                $attributes[Comment::WRITER_REF],
                $attributes[Comment::WATERBOTTLE_REF],
                $attributes[Comment::WRITING_DATE_REF],
                $attributes[Comment::COMMENT_REF]);
        }

        public function toArray() : array{
            return array(
                Comment::WRITER_REF => $this->writer,
                Comment::WATERBOTTLE_REF => $this->waterbottle,
                Comment::WRITING_DATE_REF => $this->writingDate,
                Comment::COMMENT_REF => $this->comment
            );
        }

    }
