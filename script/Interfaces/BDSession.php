<?php

namespace Scripts\Interfaces;

interface BDSession
{
    public function getRaitingListQueary();

    public function getUsersName($newUserLogin);

    public function setNewUserOnBD($newUserLogin, $newUserPass);

    public function getHeaderTaskQuery();

    public function getPatternForCodeSpaceQuery();

    public function getListOfTasksQuery();

    public function findCreatedUser($userLogin);
}