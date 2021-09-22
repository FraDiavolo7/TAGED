<?php
/*
 * Copyright © 2013 Diveen
 * All Rights Reserved.
 *
 * This software is proprietary and confidential to Diveen ReplayParser
 * and is protected by copyright law as an unpublished work.
 *
 * Unauthorized access and disclosure strictly forbidden.
 */

/**
 * @author Mickaël Martin-Nevot
 */

class Player {
    /**
     * @var $player is p1 or p2 most of the time, may also be p3 or p4 in 4 players battles.
     */
    private $player;

    /**
     * @var $username is the username.
     */
    private $username;

    /**
     * @var $avatar is the player's avatar identifier (usually a number, but other values can be used for custom avatars).
     */
    private $avatar;

    /**
     * @var $rating is the player's Elo rating in the format they're playing. This will only be displayed in rated battles and when the player is first introduced otherwise it's blank.
     */
    private $rating;


    /**
     * Player constructor.
     * @param $player
     * @param $username
     * @param $avatar
     * @param $rating
     */
    public function __construct($player, $username, $avatar, $rating) {
        $this->player = $player;
        $this->username = $username;
        $this->avatar = $avatar;
        $this->rating = $rating;
    }

    /**
     * @return is $player.
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param $player
     */
    public function setPlayer($player)
    {
        $this->player = $player;
    }

    /**
     * @return is $username.
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return is $avatar.
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return is $rating.
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }
}