
/**
CUS_ID
    Unique Id of the customer

CUS_NAME
    Name of the customer

CUS_EMAIL
    Unique email of the customer
*/
CREATE TABLE CUSTOMER (
    CUS_ID INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    CUS_NAME VARCHAR(100),
    CUS_EMAIL VARCHAR(100)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

ALTER TABLE CUSTOMER ADD UNIQUE INDEX (CUS_+EMAIL);


/**
SPO_ID
    Id of the Special Offer

SPO_NAME
    Name of the special offer

SPO_PERCENTAGE
    Percentage of the discount

SPO_URL
    URL of this offer, can have a page with a full content

SPO_CODE
    The code to this offer

SPO_EXPIRATION_DATETIME
    Date and time when this Special Offer should expires
*/
CREATE TABLE SPECIAL_OFFER (
    SPO_ID INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    SPO_NAME VARCHAR(100),
    SPO_PERCENTAGE NUMERIC(10,2),
    SPO_URL VARCHAR(255),
    SPO_CODE VARCHAR(20),
    SPO_DATETIME_EXPIRATION TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

ALTER TABLE SPECIAL_OFFER ADD UNIQUE INDEX (SPO_CODE);
ALTER TABLE SPECIAL_OFFER ADD UNIQUE INDEX (SPO_URL);


/**
VOU_ID
    Id of the Voucher

CUS_ID
    Id of the related Customer

SPO_ID
    Id of the related Special Offer

VOU_DATETIME_USED
    Datetime of when this voucher been used

VOU_DATETIME_CREATED
    Datetime of when this voucher was created

VOU_DATETIME_START
    Datetime of when this voucher will be available

VOU_DATETIME_EXPIRATION
    Datetime of when this voucher will expires

VOU_STATUS
    Statis of the voucher
    1 = Pending to be used
    2 = Already used
    3 = Expired
*/
CREATE TABLE VOUCHER (
    VOU_ID INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    CUS_ID INTEGER NOT NULL,
    SPO_ID INTEGER NOT NULL,
    VOU_DATETIME_USED TIMESTAMP,
    VOU_DATETIME_CREATED TIMESTAMP,
    VOU_DATETIME_START TIMESTAMP,
    VOU_DATETIME_EXPIRATION TIMESTAMP,
    VOU_STATUS SMALLINT
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

ALTER TABLE VOUCHER ADD CONSTRAINT FK_CUS_ID FOREIGN KEY (CUS_ID) REFERENCES CUSTOMER (CUS_ID) ON DELETE RESTRICT;
ALTER TABLE VOUCHER ADD CONSTRAINT FK_SPO_ID FOREIGN KEY (SPO_ID) REFERENCES SPECIAL_OFFER (SPO_ID) ON DELETE RESTRICT;
ALTER TABLE VOUCHER ADD INDEX (CUS_ID, SPO_ID);
ALTER TABLE VOUCHER ADD INDEX (VOU_STATUS);


/**
VOE_ID
    Id of this Voucher Event

VOU_ID
    Id of the related Voucher

VOE_DATETIME
    Date and time of this event

VOE_DESCRIPTION
    Description of this event
*/
CREATE VOUCHER_EVENT (
    VOE_ID INTEGER NOT NULL PRIMARY KEY,
    VOU_ID INTEGER AUTO_INCREMENT,
    VOE_DATETIME TIMESTAMP,
    VOE_DESCRIPTION_EVENT VARCHAR(255)
)

ALTER TABLE VOUCHER_EVENT ADD CONSTRAING FOREIGN KEY (VOU_ID) REFERENCES VOUCHER (VOU_ID) ON DELETE RESTRICT;





CREATE TRIGGER TR_VOU_AF AFTER INSERT ON VOUCHER
FOR EACH ROW BEGIN
    /* Insert an event related to the new voucher */
    INSERT INTO VOUCHER_EVENT (VOU_ID, VOU_DATETIME, VOU_DESCRIPTION_EVENT)
    VALUE (NEW.VOU_ID, CURRENT_TIMESTAMP, 'Created the voucher');
END;


CREATE TRIGGER TR_VOU_AU AFTER UPDATE ON VOUCHER
FOR EACH ROW BEGIN
    /* Insert an event related to the new voucher */
    IF (NEW.VOU_STATUS = 3 AND OLD.VOU_STATUS <> 3) THEN
        INSERT INTO VOUCHER_EVENT (VOU_ID, VOU_DATETIME, VOU_DESCRIPTION_EVENT)
        VALUE (NEW.VOU_ID, CURRENT_TIMESTAMP, 'Voucher was expired');
    END IF;
END;


DROP TRIGGER TR_SPO_AF;
CREATE TRIGGER TR_SPO_AF BEFORE INSERT ON SPECIAL_OFFER
FOR EACH ROW BEGIN
    declare v_url VARCHAR(100);

    /* Populate the Code field */
    SET NEW.SPO_CODE = CONCAT(DATE_FORMAT("2017-06-15", "%Y%m%d"), FLOOR(RAND()*(9999-1000+1) + 1000));

    /* Populate the URL field */
    SET v_url = concat(
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1),
        substring('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*62+1, 1)
    );
                        
    SET NEW.SPO_URL = v_url;
END;





DROP PROCEDURE ADD_CUSTOMER;
CREATE PROCEDURE ADD_CUSTOMER (
    usu_id INTEGER,
    usu_auth VARCHAR(100),
    name VARCHAR(100),
    email VARCHAR(100)
)
BEGIN
    /**
    Insert a new customer

    :param usu_id INTEGER:
        The Id of the user that executes this SP

    :param usu_auth VARCHAR(100):
        The authentication hash of the user that executes this SP

    :param name VARCHAR(100):
        The name of the customer

    :param email VARCHAR(100):
        The email of the customer
    */

    /* Insert the new customer only if does not exists other with the same email */
    IF (NOT EXISTS(SELECT CUS_ID FROM CUSTOMER WHERE CUS_EMAIL = email)) THEN
        INSERT INTO CUSTOMER (CUS_NAME, CUS_EMAIL)
        VALUE (name, email);
    END IF;
END;


DROP PROCEDURE EDIT_CUSTOMER;
CREATE PROCEDURE EDIT_CUSTOMER (
    usu_id INTEGER,
    usu_auth VARCHAR(100),
    id INTEGER,
    name VARCHAR(100),
    email VARCHAR(100)
)
BEGIN
    /**
    Edit a customer

    :param usu_id INTEGER:
        The Id of the user that executes this SP

    :param usu_auth VARCHAR(100):
        The authentication hash of the user that executes this SP

    :param cus_id INTEGER:
        The Id of the Customer

    :param name VARCHAR(100):
        The name of the customer

    :param email VARCHAR(100):
        The email of the customer
    */

    /* Edit the customer only if does not exists other same email */
    IF (NOT EXISTS(SELECT CUS_ID FROM CUSTOMER WHERE CUS_EMAIL = email AND CUS_ID <> id)) THEN
        UPDATE CUSTOMER
        SET
            CUS_NAME = name,
            CUS_EMAIL = email
        WHERE CUS_ID = id;
    END IF;
END;


DROP PROCEDURE DEL_CUSTOMER;
CREATE PROCEDURE DEL_CUSTOMER (
    usu_id INTEGER,
    usu_auth VARCHAR(100),
    id INTEGER
)
BEGIN
    /**
    Edit a customer

    :param usu_id INTEGER:
        The Id of the user that executes this SP

    :param usu_auth VARCHAR(100):
        The authentication hash of the user that executes this SP

    :param cus_id INTEGER:
        The Id of the Customer
    */

    DELETE FROM CUSTOMER WHERE CUS_ID = id;
END;


DROP PROCEDURE ADD_SPECIAL_OFFER;
CREATE PROCEDURE ADD_SPECIAL_OFFER (
    usu_id INTEGER,
    usu_auth VARCHAR(100),
    name VARCHAR(100),
    percentage NUMERIC(10,2),
    url VARCHAR(255),
    code VARCHAR(20),
    datetime_expiration TIMESTAMP
)
BEGIN
    /**
    Insert a new special offer

    :param usu_id INTEGER:
        The Id of the user that executes this SP

    :param usu_auth VARCHAR(100):
        The authentication hash of the user that executes this SP

    :param name VARCHAR(100):
        The name of the special offer

    :param percentage NUMERIC(10,2):
        The email of the special offer

    :param url VARCHAR(255):
        The URL of the especial offer

    :param code VARCHAR(20):
        The code of the special offer

    :param expiration TIMESTAMP:
        The date/time expiration of the special offer
    */

    /* Insert the new especial offer only if does not exists other with the same code */
    IF (NOT EXISTS(SELECT SPO_ID FROM SPECIAL_OFFER WHERE SPO_CODE = code)) THEN
        INSERT INTO SPECIAL_OFFER (SPO_NAME, SPO_PERCENTAGE, SPO_URL, SPO_CODE, SPO_DATETIME_EXPIRATION)
        VALUE (name, percentage, url, code, datetime_expiration);
    END IF;
END;


DROP PROCEDURE EDIT_SPECIAL_OFFER;
CREATE PROCEDURE EDIT_SPECIAL_OFFER (
    usu_id INTEGER,
    usu_auth VARCHAR(100),
    id INTEGER,
    name VARCHAR(100),
    percentage NUMERIC(10,2),
    url VARCHAR(255),
    code VARCHAR(20),
    expiration TIMESTAMP
)
BEGIN
    /**
    Insert a new special offer

    :param usu_id INTEGER:
        The Id of the user that executes this SP

    :param id INTEGER:
        The Id of the Special Offer

    :param usu_auth VARCHAR(100):
        The authentication hash of the user that executes this SP

    :param name VARCHAR(100):
        The name of the special offer

    :param percentage NUMERIC(10,2):
        The email of the special offer

    :param url VARCHAR(255):
        The URL of the especial offer

    :param code VARCHAR(20):
        The code of the special offer

    :param expiration TIMESTAMP:
        The date/time expiration of the special offer
    */

    /* Update the special offer only if doen't exists other with the same new Code */
    IF (NOT EXISTS(SELECT SPO_ID FROM SPECIAL_OFFER WHERE SPO_CODE = code AND SPO_ID <> id)) THEN
        UPDATE SPECIAL_OFFER
        SET
            SPO_NAME = name,
            SPO_PERCENTAGE = percentage,
            SPO_URL = url,
            SPO_CODE = code,
            SPO_DATETIME_EXPIRATION = expiration
        WHERE SPO_ID = id;
    END IF;
END;


DROP PROCEDURE DEL_SPECIAL_OFFER;
CREATE PROCEDURE DEL_SPECIAL_OFFER (
    usu_id INTEGER,
    usu_auth VARCHAR(100),
    id INTEGER
)
BEGIN
    /**
    Insert a new special offer

    :param usu_id INTEGER:
        The Id of the user that executes this SP

    :param id INTEGER:
        The Id of the Special Offer

    :param usu_auth VARCHAR(100):
        The authentication hash of the user that executes this SP

    :param name VARCHAR(100):
        The name of the special offer

    :param percentage NUMERIC(10,2):
        The email of the special offer

    :param url VARCHAR(255):
        The URL of the especial offer

    :param code VARCHAR(20):
        The code of the special offer

    :param expiration TIMESTAMP:
        The date/time expiration of the special offer
    */

    /* Only remove the special offer if no customer has used yet */
    IF (NOT EXISTS(SELECT VOUCHER.VOU_ID FROM VOUCHER WHERE SPO_ID = id AND VOU_STATUS = 2)) THEN
        DELETE FROM SPECIAL_OFFER WHERE SPO_ID = id;
    END IF;
END;



DROP PROCEDURE ADD_VOUCHER;
CREATE PROCEDURE ADD_VOUCHER (
    usu_id INTEGER,
    usu_auth VARCHAR(100),
    p_cus_id INTEGER,
    p_spo_id INTEGER
)
BEGIN
    IF (NOT EXISTS(SELECT VOU_ID FROM VOUCHER WHERE CUS_ID = p_cus_id AND SPO_ID = p_spo_id AND VOU_STATUS = 1)) THEN
        INSERT INTO VOUCHER (CUS_ID, SPO_ID, VOU_DATETIME_CREATED, VOU_STATUS)
        VALUE (p_cus_id, p_spo_id, CURRENT_TIMESTAMP, 1);
    END IF;
END;



DROP PROCEDURE USE_VOUCHER;
CREATE PROCEDURE USE_VOUCHER (
    usu_id INTEGER,
    usu_auth VARCHAR(100),
    id INTEGER
)
BEGIN
    UPDATE VOUCHER SET VOU_STATUS = 2 WHERE VOU_ID = id;
END;












