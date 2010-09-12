<?php
	  function F_RIJNDAEL128_EncryptCBC ($Args)
	    {
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CBC)
	    }

	  function F_RIJNDAEL128_DecryptCBC ($Args)
	    {
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CBC)
	    }
	  function F_RIJNDAEL128_EncryptCFB ($Args)
	    {
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CFB)
	    }

	  function F_RIJNDAEL128_DecryptCFB ($Args)
	    {
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CFB)
	    }
	  function F_RIJNDAEL128_EncryptCTR ($Args)
	    {
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CTR)
	    }

	  function F_RIJNDAEL128_DecryptCTR ($Args)
	    {
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CTR)
	    }
	  function F_RIJNDAEL128_EncryptECB ($Args)
	    {
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_ECB)
	    }

	  function F_RIJNDAEL128_DecryptECB ($Args)
	    {
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_ECB)
	    }
	  function F_RIJNDAEL128_EncryptNCFB ($Args)
	    {
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_NCFB)
	    }

	  function F_RIJNDAEL128_DecryptNCFB ($Args)
	    {
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_NCFB)
	    }
	  function F_RIJNDAEL128_EncryptNOFB ($Args)
	    {
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_NOFB)
	    }

	  function F_RIJNDAEL128_DecryptNOFB ($Args)
	    {
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_NOFB)
	    }
	  function F_RIJNDAEL128_EncryptOFB ($Args)
	    {
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_OFB)
	    }

	  function F_RIJNDAEL128_DecryptOFB ($Args)
	    {
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_OFB)
	    }
	  function F_RIJNDAEL128_EncryptSTREAM ($Args)
	    {
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_STREAM)
	    }

	  function F_RIJNDAEL128_DecryptSTREAM ($Args)
	    {
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_STREAM)
	    }