<?php

interface Twm_Nosql_Mongo_Document_Interface
extends Twm_Nosql_Mongo_Object_Interface {

	public function __construct($data, Twm_Nosql_Mongo_Collection $collection);

	public function getId();

	public function getCollection();

	public function save();

	public function isChanged();

	public function commit();
}

