<?php
namespace StudentCheckIn;
interface IEntity {
    /**
     * Summary of ValidateAsUpdate
     * @return boolean
     */
    public function ValidateAsUpdate();

	/**
     * Summary of ValidateAsInsert
     * @return boolean
     */
	public function ValidateAsInsert();
}

