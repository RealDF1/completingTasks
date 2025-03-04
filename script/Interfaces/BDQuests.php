<?php

namespace Scripts\Interfaces;

interface BDQuests
{
    public function setTaskToTable();

    public function getCompletedCode();

    public function setLikeQuery();

    public function getQuest($task_id);

    public function setCompletedCodeToTable();

    public function setRaitingUserOnComplete($addRating);

}