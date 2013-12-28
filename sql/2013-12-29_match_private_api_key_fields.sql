ALTER TABLE  subscribers CHANGE  session_api_token  api_key_private VARCHAR( 32 ) NULL DEFAULT NULL 
COMMENT  'API key for authorizing on installation.';