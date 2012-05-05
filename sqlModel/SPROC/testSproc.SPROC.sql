DROP PROCEDURE IF EXISTS testSproc;  
DELIMITER //

CREATE PROCEDURE `testSproc` ()
LANGUAGE SQL
DETERMINISTIC
SQL SECURITY DEFINER
COMMENT 'A procedure'
BEGIN
  DECLARE ObjectType  VARCHAR(255);
  DECLARE ObjectName VARCHAR(255);
  DECLARE ValueName VARCHAR(255);
  DECLARE Value VARCHAR(255);
  
  SET ObjectType = 'TABLE';
  SET ObjectName = 'openemr_extendedproperties';
  SET ValueName = 'Description';
  SET Value = 'A Table for storing Extended Properties';
  
  INSERT INTO openemr_extendedproperties (ObjectType, ObjectName, ValueName, Value )
  VALUES (ObjectType, ObjectName, ValueName, Value );
END//

