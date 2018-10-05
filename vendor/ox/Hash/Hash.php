<?php namespace ox\Hash;



class Hash {
    
    protected $rounds = 10;
    
    public function __construct() {
        
    }
    

	public function make($value, array $options = array())
	{
		$cost = isset($options['rounds']) ? $options['rounds'] : $this->rounds;

		$hash = password_hash($value, PASSWORD_BCRYPT, array('cost' => $cost));

		if ($hash === false)
		{
			throw new \RuntimeException("Bcrypt hashing not supported.");
		}

		return $hash;
	}

}