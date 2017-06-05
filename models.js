var config 		=	require('./config');
var dbconn =config.createConnection();

var async = require('async');
var utils 		= 	require('../utils');

var OTP_DELETION_TIME =1*60*1000;  // in minute

function signUp(data, cb) {
    function duplicateEmailChecking(callback) {
        dbconn.query('SELECT COUNT(user_id) counter FROM tbl_user WHERE email="' + data.email + '"', function (err, rows, fields) {
            //dbconn.end();
            if (!err) {
                if (rows[0].counter > 0) {
                    callback(null, "DUPEMAIL");
                } else {
                    callback(null, "OK");
                }
            } else {
                callback(err, "ERROR");
            }
        })
    }

    function duplicateMobileChecking(callback) {
        dbconn.query('SELECT COUNT(user_id) counter FROM tbl_user WHERE phone="' + data.phone + '"', function (err, rows, fields) {
            //dbconn.end();
            if (!err) {
                if (rows[0].counter > 0) {
                    callback(null, "DUPMOB");
                } else {
                    callback(null, "OK");
                }
            } else {
                callback(err, "ERROR");
            }
        })
    }
    
    function createAdmin(callback){
        dbconn.query('select * from tbl_user where username="admin"',function (err, rows, fields) {
            if(err){
                callback(err, "ERROR");
            }else{
                if(rows.length == 0){
                    dbconn.query('INSERT INTO tbl_user SET countryCode="+91",email="admin.dnow@gmail.com",phone="9876543210",username="admin",password="1234",status="1",is_deleted="0",user_type="1",image="test.jpg"', function (err, rows, fields) {
                        if(err){
                            callback(err, "ERROR");
                        }else{
                            console.log("Admin Created");
                            callback(null, "Admin Created");
                        }
                    });
                }else{
                    console.log("Admin already Created");
                    callback(null, "Admin already Created");
                }

            }
        })
    }

    async.parallel([duplicateEmailChecking, duplicateMobileChecking,createAdmin], function (err, results) {
        if (err) {
            console.error(err);
            cb(err, "ERROR");
        } else {
            if (results[0] == "DUPEMAIL") {
                cb(null, "DUPEMAIL");
            } else if (results[1] == "DUPMOB") {
                cb(null, "DUPMOB");
            } else {
                dbconn.query('INSERT INTO tbl_user SET countryCode="' + data.countryCode + '",email="' + data.email + '",phone="' + data.phone + '",username="' + data.username + '",password="' + data.password + '",status="0",is_deleted="0",user_type="' + data.usertype + '",image="'+data.image+'"', function (err, rows, fields) {
                    if (!err) {
                        var userID = rows.insertId;
                        //console.log("otp: " + data.otp);
                        deleteOtpByUserId(userID,function (err,deleted) {
                            if(err){
                                console.error(err);
                                cb(err, "ERROR");
                            }else{
                                dbconn.query('INSERT INTO tbl_otp SET user_id="' + rows.insertId + '",otp=' + data.otp, function (err, row1s, field1s) {
                                    if (err) {
                                        console.log(err);
                                        cb(err, "ERROR");
                                    } else {
                                        setTimeout(function () {
                                            deleteOtpByUserId(userID,function(err,deleted){
                                                if(err){
                                                    console.error(err);
                                                }else{
                                                    console.log("OTP deletd for userid "+userID);
                                                }
                                            })
                                        },OTP_DELETION_TIME);
                                        cb(null, userID+"");
                                    }
                                })
                            }
                        });

                    } else {
                        cb(err, "ERROR");
                    }
                })
            }
        }
    })
}
function signIn(data, cb) {
    dbconn.query('SELECT status,user_id,username,email,phone,countryCode,image,other_details,is_blocked FROM tbl_user WHERE (email="' + data.username + '" OR CONCAT(countryCode,phone)="' + data.username + '" OR phone="' + data.username + '") AND password="' + data.password + '"', function (err, rows, fields) {
        if (err) {
            console.log(err);
            console.log("inside error")
            cb(err, false)
        } else if (rows.length == 0) {
            var resp = {type: "NOTMATCHED"};
            cb(null, resp);
        }else if(rows[0].is_blocked === "1"){
            cb(null,{type: "BLOCKED"})
        } else {
            var type="OK";
            if (rows[0].status == "0"){
                type="NOTVERIFIED";
            }
            getProfile({userId:rows[0].user_id},function (err,data) {
                if(err){
                    console.log(err);
                    cb(err, false)
                }else{
                    console.log(JSON.stringify(data))
                    if(data == "NOT_FOUND"){
                        cb(null,"NOT_FOUND");
                    }else{
                        data.type=type;
                        cb(null,data);
                    }
                }
            });

        }
    })
}
function getProfile(data,cb) {
    function getUserDetails(callback) {
        dbconn.query('select username,email,phone,image,other_details from tbl_user WHERE user_id="' + data.userId + '"', function (err, rows, fields) {
            if (err) {
                console.log(err);
                callback(err, false)
            } else {
                if (rows.length > 0) {
                    callback(null, rows[0]);
                } else {
                    callback(null, "NOT_FOUND");
                }
            }
        });
    }
    function getBankDetails(callback) {
        dbconn.query('SELECT acc_holder_name name,bank_name bank,ifsc,acc_no accNo FROM tbl_bank WHERE user_id="' + data.userId + '"', function (errb, rowsb, fieldsb) {
            if (errb) {
                callback(errb, false);
            } else {
                if (rowsb.length == 0) {
                    var bankDetails = {};
                    callback(null, bankDetails);
                } else {
                    var bankDetails = rowsb[0];
                    callback(null, bankDetails);
                }
            }
        })
    }
    function getWalletDetails(callback) {
        walletBalance(data,function (err,details) {
            if(err){
                callback(errb, false);
            }else{
                callback(null, details);
            }
        })
    }
    async.parallel([getUserDetails,getBankDetails,getWalletDetails],function(err,results){
        if(err){
            cb(err, false)
        }else{
            if(results[0]=="NOT_FOUND"){
                cb(null, "NOT_FOUND");
            }else{
                var other_details =null;
                if(results[0].other_details != null){
                    other_details=results[0].other_details;
                }
                var response = {
                    "userId":data.userId,
                    "name": results[0].username,
                    "email": results[0].email,
                    "phone": results[0].phone,
                    "image": results[0].image,
                    "address": other_details,
                    "bankDetails": results[1],
                    "walletBalance":results[2].wallet
                };
                cb(null, response);
            }
        }
    })
}
function verifyUser(data, cb) {
    data.otp = Number(data.otp);
    console.log(JSON.stringify(data))
    dbconn.query('SELECT * FROM tbl_otp WHERE user_id="' + data.user_id + '" AND otp="' + data.otp + '"', function (err, rows, fields) {
        if (err) {
	    console.error(err);
            cb(err, "ERROR!!!")
        } else {
            if (rows.length > 0) {
                console.log(JSON.stringify(rows))
                dbconn.query('UPDATE tbl_user SET status="1" WHERE user_id="' + data.user_id + '"', function (err, rows, fields) {
                    if (err) {
			console.error(err);
                        cb(err, "ERROR!!!")
                    } else {
                        dbconn.query('DELETE FROM tbl_otp WHERE user_id="' + data.user_id + '" AND otp="' + data.otp + '"', function (err4, rows4, fields4) {
                            if (err4) {
                                console.log(err4);
                                console.log('DELETE FROM `tbl_otp` WHERE `user_id`="' + data.user_id + '" AND `otp`="' + data.otp + '"');
                                cb(err4, "ERROR!!!")
                            } else {
				var user={};
                                user.userId=data.user_id;
				getProfile(user,function(err,details){
                                    if(err){
                                        console.error(err)
                                        cb(err,"ERROR")
                                    }else{
                                        if(details === "NOT_FOUND"){
                                            cb(null,{})
                                        }else{
                                            var resp = {
                                                type: "OK",
                                                userId: details.userId,
                                                name: details.name,
                                                email: details.email,
                                                phone: details.phone,
                                                image: details.image,
                                                address: details.address,
                                                bankDetails: details.bankDetails
                                            };
                                            cb(null, resp)
                                        }
                                    }
                                })
                                /*dbconn.query('SELECT status,user_id,username,email,phone,image,other_details FROM tbl_user WHERE user_id="' + data.user_id + '" AND is_blocked="0"', function (err3, rows3, fields3) {
                                    if (err3) {
					console.error(err3);
                                        cb(err3, "ERROR!!!")
                                    } else {
					console.log("-->"+JSON.stringify(rows3[0]));
                                         dbconn.query('SELECT acc_holder_name name,bank_name bank,ifsc,acc_no accNo FROM tbl_bank WHERE user_id="' + data.user_id + '"', function (errb, rowsb, fieldsb) {
                                            if(errb){
						console.error(err);
                                                 cb(errb, false)
                                             } else {
                                                if(rowsb.length==0){
                                                    var bankDetails     =   {};
                                                } else {
                                                    var bankDetails     =   rowsb[0];
                                                }
                                                var addressd;
                                                if (rows3[0].other_details != null) {
                                                    var addressData =rows3[0].other_details;
                                                    if (addressData.address != null) {
                                                        addressd = addressData.address;
                                                    }
                                                }
                                                var resp = {
                                                    type: "OK",
                                                    userId: rows3[0].user_id,
                                                    name: rows3[0].username,
                                                    email: rows3[0].email,
                                                    phone: rows3[0].phone,
                                                    image: rows3[0].image,
                                                    address: addressd,
                                                    bankDetails: bankDetails
                                                };
                                                cb(null, resp)
                                            }
                                        })
                                    }
                                })*/
                            }
                        })
                    }
                })
            } else {
                cb(null, "INVALIDCODE")
            }
        }
    })
}

function forgotPassword(data, cb) {
    console.log(JSON.stringify(data));
    dbconn.query('SELECT status,user_id,username,email,phone,image,other_details FROM tbl_user WHERE (phone="' + data.phone + '" OR CONCAT(countryCode,phone)="' + data.phone + '" OR CONCAT(countryCode,phone)="+' + data.phone + '")', function (err, rows, fields) {
        if (err) {
	    console.error(err);
            cb(err, false);
        } else {
            if (rows.length > 0) {
		console.log(JSON.stringify(rows[0]));
                var userid = rows[0].user_id;
                deleteOtpByUserId(userid,function (err,deleted) {
                    if (err) {
                        console.error(err);
                        cb(err, "ERROR");
                    } else {
                        dbconn.query('INSERT  INTO tbl_otp SET user_id="' + rows[0].user_id + '",otp="' + data.otp + '"', function (err, rows3, fields) {
                            if (err) {
				console.error(err);
                                cb(err, false);
                            } else {
                                var addressd;
                                if (rows[0].other_details != null) {
                                    var addressData = rows[0].other_details;
                                    if (addressData.address != null) {
                                        addressd = addressData.address;
                                    }
                                }
                                var resp = {
                                    success: true,
                                    userId: rows[0].user_id,
                                    username: rows[0].username,
                                    email: rows[0].email,
                                    phone: rows[0].phone,
                                    image: rows[0].image,
                                    address: addressd
                                };
                                setTimeout(function () {
                                    deleteOtpByUserId(userid,function(err,deleted){
                                        if(err){
					    
                                            console.error(err);
                                        }else{
                                            console.log("OTP deletd for userid "+userid);
                                        }
                                    })
                                },OTP_DELETION_TIME);
                                cb(null, resp);
                            }
                        })
                    }
                });

            } else {
                cb(null, "NOUSER")
            }
        }
    })
}
function resetPassword(data, cb) {
    dbconn.query('SELECT * FROM tbl_user WHERE user_id="' + data.userId + '" AND password="' + data.oldPassword + '"', function (err, rows, fields) {
        if (err) {
            cb(err, false)
        } else {
            console.log()
            if (rows.length == 0) {
                cb(null, false);
            } else {
                dbconn.query('UPDATE tbl_user SET password="' + data.password + '" WHERE user_id="' + data.userId + '"', function (err, rows, fields) {
                    if (err) {
                        cb(err, false)
                    } else {
                        cb(null, true);
                    }
                })
            }
        }
    })
}
function changePassword(data, cb) {
    dbconn.query('UPDATE tbl_user SET password="' + data.password + '" WHERE user_id="' + data.userId + '"', function (err, rows, fields) {
        if (err) {
            cb(err, false)
        } else {
            cb(null, true);
        }
    });
}
function editProfile(data, cb) {
    function duplicateEmailChecking(callback) {
        dbconn.query('SELECT COUNT(user_id) counter FROM tbl_user WHERE email="' + data.email + '" AND user_id!="' + data.userId + '"', function (err, rows, fields) {
            if (!err) {
                console.log(rows[0].counter)
                if (rows[0].counter > 0) {
                    callback(null, "DUPEMAIL");
                } else {
                    callback(null, "OK");
                }
            } else {
                callback(err, "ERROR");
            }
        })
    }

    function duplicateMobileChecking(callback) {
        dbconn.query('SELECT COUNT(user_id) counter FROM tbl_user WHERE phone="' + data.phone + '" AND user_id!="' + data.userId + '"', function (err, rows, fields) {
            if (!err) {
                if (rows[0].counter > 0) {
                    callback(null, "DUPMOB");
                } else {
                    callback(null, "OK");
                }
            } else {
                callback(err, "ERROR");
            }
        })
    }

    async.parallel([duplicateEmailChecking, duplicateMobileChecking], function (err, results) {
        if (err) {
            cb(err, false);
        } else {
            if (results[0] == "DUPEMAIL") {
                cb(null, "DUPEMAIL");
            } else if (results[1] == "DUPMOB") {
                cb(null, "DUPMOB");
            } else {
                var addrData = "{\"address\": \""+data.address+"\"}";
                //console.log(typeof addrData);
                dbconn.query('UPDATE tbl_user SET username="' + data.username + '",image="'+data.image+'",email="' + data.email + '",phone="' + data.phone + '",other_details=\'' + data.address + '\'   WHERE user_id="' + data.userId + '"', function (err1, rows, fields) {
                    if (err1) {
                        cb(err1, false);
                        console.log("errorr")
                        console.log(err1);
                    } else {

                        dbconn.query('SELECT COUNT(id) counter FROM tbl_bank WHERE user_id="'+data.userId+'"',function (err, rows, fields){
                            if(err){

                                console.error("errorr")
                                console.error(err);
                                cb(err, false);
                            }else{
                                if (rows[0].counter > 0) {
                                    dbconn.query('UPDATE tbl_bank SET acc_holder_name="' + data.acc_holder_name + '",bank_name="' + data.bank_name + '",ifsc="' + data.ifsc + '",acc_no="' + data.acc_no + '"  WHERE user_id="' + data.userId + '"',function (err, rows, fields) {
                                       if(err){
                                           console.error(err);
                                           cb(err, false);
                                       } else{
                                           cb(null, "OK");
                                       }
                                    })
                                } else {
                                    var newdata={"userId":data.userId,"name":data.acc_holder_name,"bank":data.bank_name,"ifsc":data.ifsc,"accNo":data.acc_no };
                                    addBankDetails(newdata,function (err,data) {
                                        if(err){
                                            console.error(err);
                                            cb(err, false);
                                        }else{
                                            if(data == "OK"){
                                                cb(null, "OK");
                                            }else{
                                                cb(null, "NOUSER");
                                            }
                                        }
                                    })
                                }

                            }
                        });

                    }
                })
            }
        }
    })

}

function addBankDetails(data, cb) {
    dbconn.query('SELECT * FROM tbl_user WHERE user_id="' + data.userId + '"', function (err2, rows2, fields2) {
        if (err2) {
            cb(err2, false);
        } else {
            if (rows2.length == 0) {
                cb(null, "NOUSER");
            } else {
                dbconn.query('INSERT INTO tbl_bank SET user_id="' + data.userId + '",acc_holder_name="' + data.name + '",bank_name="' + data.bank + '",ifsc="' + data.ifsc + '",acc_no="' + data.accNo + '",status="1",details="'+data.details+'"', function (err, rows, fields) {
                    if (err) {
                        cb(err, false);
                    } else {
                        cb(null, "OK");
                    }
                })
            }
        }
    })
}

function cautionMoneyDeposit(data, cb) {

    dbconn.query('SELECT balance FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="CAUTION_WALLET" ORDER BY last_updated DESC LIMIT 1', function (err, rows, fields) {
        if (err) {
            console.log(err);
            cb(err, false)
        } else {
            if (rows.length > 0) {
                var totalBalance = parseFloat(rows[0].balance) + parseFloat(data.amount);
            } else {
                var totalBalance = data.amount;
            }
            dbconn.query('INSERT INTO tbl_userpayment SET user_id="' + data.userId + '",amount="' + data.amount + '",balance="' + totalBalance + '",is_incoming="1",status="SUCCESS",payment_type="CAUTION_WALLET",operation="DEPOSIT"', function (err1, rows1, fields1) {
                if (err1) {
                    cb(err1, false)
                } else {
                    var txnid   =   utils.generateTxn()+rows1.insertId;
                    dbconn.query('UPDATE tbl_userpayment SET txnId="'+txnid+'" WHERE id="' + rows1.insertId + '" ', function (err3, rows3, fields3) {
                        if (err3) {
                            cb(err3, false)
                        } else {
                            dbconn.query('SELECT balance FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="DISPUTE_WALLET" AND status="SUCCESS" ORDER BY last_updated DESC LIMIT 1', function (err2, rows2, fields2) {
                                if (err2) {
                                    cb(err2, false)
                                } else {
                                    if (rows2.length > 0) {
                                        var disputeBalance = parseFloat(err2[0].balance);
                                    } else {
                                        var disputeBalance = 0;
                                    }
                                    var response = {
                                            "txnId": txnid,
                                            "amount": data.amount,
                                            "wallet": totalBalance,
                                            "disputeWallet": disputeBalance
                                    };
                                    dbconn.query('select * from tbl_userpayment where user_id="1"',function (err, rows, fields) {
                                        if (err) {
                                            console.log(err);
                                            cb(err, false)
                                        }else{
                                            if(rows.length == 0 ){
                                                dbconn.query('INSERT INTO tbl_userpayment SET user_id="1",amount="0",balance="0",is_incoming="1",status="SUCCESS",payment_type="CAUTION_WALLET",operation="DEPOSIT"', function (err1, rows1, fields1) {
                                                    if (err) {
                                                        console.log(err);
                                                        cb(err, false)
                                                    }else{
                                                        cb(null, response);
                                                    }
                                                });
                                            }else{
                                                cb(null, response);
                                            }
                                        }
                                    });


                                }
                            })
                        }
                    })
                }
            });       
        }
    })
}

function approvedPayout(data, cb) { 

             dbconn.query('SELECT * FROM `tbl_payout_request` WHERE `id`='+data.requestId, function (err2, rows2, fields2) {

                if (err2) {
            console.log(err2);
            cb(err2, false)
        }  else {
                   if (rows2.length > 0) {
                         var payoutamout = rows2[0].amount;
                           dbconn.query('SELECT balance FROM tbl_userpayment WHERE user_id="' + data.requestId + '" AND payment_type="CAUTION_WALLET" ORDER BY last_updated DESC LIMIT 1', function (err3, rows3, fields3) {
                   if (err3) {
                   console.log(err3);
                   cb(err3, false)
                   } else {
                        if (rows3.length > 0) {
                            if(payoutamout >= rows3[0].balance){
                                cb({"errormsg":"Payoutamount is greater than balance"}, false) ;
                            }else{
                                var totalBalance = parseFloat(rows3[0].balance) - payoutamout;
                                    dbconn.query('INSERT INTO tbl_userpayment SET user_id="' + data.requestId + '",amount="' + payoutamout + '",balance="' + totalBalance + '",is_incoming="1",status="SUCCESS",payment_type="CAUTION_WALLET",operation="DEPOSIT"', function (err4, rows4, fields4) {
                                    if (err4) {
                                     cb(err4, false)
                                     } else {
                                     var txnid   =   utils.generateTxn()+rows4.insertId;
                                     dbconn.query('UPDATE tbl_userpayment SET txnId="'+txnid+'" WHERE id="' + rows4.insertId + '" ', function (err5, rows5, fields5) {
                                    if (err5) {
                                     cb(err5, false)
                                     }else{
                                        dbconn.query('UPDATE `tbl_payout_request` SET `status` = "APPROVED" WHERE `tbl_payout_request`.`id` ='+data.requestId, function (err1, rows1, fields1) {
                                           if (err1) {
                                             console.log(err1);
                                             cb(err1, false)
                                             }
                                           })  
                                     }
                            })
                           }
                          })
                         }                        
                        }                     
                       }
                      }) 
                    } else{
                       cb({"errormsg":"No request for payout"}, false) ;
                    }
                  

                 }
             })   
      
}
function walletBalance(data, cb) {
    dbconn.query('SELECT balance FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="CAUTION_WALLET" AND status="SUCCESS" ORDER BY last_updated DESC LIMIT 1', function (err, rows, fields) {
        if (err) {
            console.log(err);
            cb(err, false)
        } else {
            if (rows.length > 0) {
                var totalBalance = parseFloat(rows[0].balance);
            } else {
                var totalBalance = 0;
            }
            dbconn.query('SELECT balance FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="DISPUTE_WALLET" AND status="SUCCESS" ORDER BY last_updated DESC LIMIT 1', function (err1, rows1, fields1) {
                if (err1) {
                    cb(err, false)
                } else {
                    if (rows1.length > 0) {
                        var disputeBalance = parseFloat(rows1[0].balance);
                    } else {
                        var disputeBalance = 0;
                    }
                    var response = {
                            "wallet": totalBalance,
                            "disputeWallet": disputeBalance
                    };
                    cb(null, response);
                }
            });       
        }
    })
}

function getTransactionHistory(data, cb) {
    var inputCheck          =   "OK";
    if(data.wallet =="CAUTION_WALLET" && data.payType=="DEPOSIT"){
        var txnHistSql      =   'SELECT txnId,balance,amount,is_incoming,status,payment_type,operation,last_updated FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="CAUTION_WALLET" AND is_incoming="1" AND operation="DEPOSIT" AND status="SUCCESS" ORDER BY last_updated DESC';
    } else if(data.wallet =="CAUTION_WALLET" && data.payType=="RECEIVE"){
        var txnHistSql      =   'SELECT txnId,balance,amount,is_incoming,status,payment_type,operation,last_updated FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="CAUTION_WALLET" AND is_incoming="0" AND status="SUCCESS" AND operation="RECEIVE" ORDER BY last_updated DESC';
    } else if(data.wallet =="CAUTION_WALLET" && data.payType=="ONHOLD"){
        var txnHistSql      =   'SELECT txnId,balance,amount,is_incoming,status,payment_type,operation,last_updated FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="CAUTION_WALLET" AND status="PENDING" ORDER BY last_updated DESC';
    } else if(data.wallet =="CAUTION_WALLET" && data.payType=="PAID"){
        var txnHistSql      =   'SELECT txnId,balance,amount,is_incoming,status,payment_type,operation,last_updated FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="CAUTION_WALLET" AND is_incoming="1" AND  status="PAID" ORDER BY last_updated DESC';
    } else if(data.wallet =="CAUTION_WALLET"){
       var txnHistSql      =   'SELECT txnId,balance,amount,is_incoming,status,payment_type,operation,last_updated FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="CAUTION_WALLET" ORDER BY last_updated DESC'; 
    } else if(data.wallet =="DISPUTE_WALLET" && data.payType=="DEPOSIT"){
        var txnHistSql      =   'SELECT txnId,balance,amount,is_incoming,status,payment_type,operation,last_updated FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="DISPUTE_WALLET" AND is_incoming="1" AND operation="DEPOSIT" AND status="SUCCESS" ORDER BY last_updated DESC';
    } else if(data.wallet =="DISPUTE_WALLET" && data.payType=="RECEIVE"){
        var txnHistSql      =   'SELECT txnId,balance,amount,is_incoming,status,payment_type,operation,last_updated FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="DISPUTE_WALLET" AND is_incoming="0" AND status="SUCCESS" AND operation="RECEIVE" ORDER BY last_updated DESC';
    } else if(data.wallet =="DISPUTE_WALLET" && data.payType=="ONHOLD"){
        var txnHistSql      =   'SELECT txnId,balance,amount,is_incoming,status,payment_type,operation,last_updated FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="DISPUTE_WALLET" AND status="PENDING" ORDER BY last_updated DESC';
    } else if(data.wallet =="DISPUTE_WALLET" && data.payType=="PAID"){
        var txnHistSql      =   'SELECT txnId,balance,amount,is_incoming,status,payment_type,operation,last_updated FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="DISPUTE_WALLET" AND is_incoming="1" AND  status="PAID" ORDER BY last_updated DESC';
    }else if(data.wallet =="DISPUTE_WALLET"){
       var txnHistSql      =   'SELECT txnId,balance,amount,is_incoming,status,payment_type,operation,last_updated FROM tbl_userpayment WHERE user_id="' + data.userId + '" AND payment_type="DISPUTE_WALLET" ORDER BY last_updated DESC'; 
    }else{
        inputCheck          =   "NOTOK";
    }
    if(inputCheck=="OK"){
        dbconn.query(txnHistSql, function (err, rows, fields) {
            if (err) {
                console.log(err);
                cb(err, false)
            } else {
                if (rows.length == 0) {
                    console.log(err);
                    cb(null, "NO_DATA")
                } else {
                    for(var i=0;i<rows.length;i++){
                        rows[i].name="Test";
                    }
                    cb(null,rows);
                }       
            }
        })
    }else{
        cb(null,"WRONG_INPUT");
    }
}
function payoutMoney(data,cb) {
    dbconn.query('SELECT balance from tbl_userpayment WHERE status="SUCCESS" and payment_type ="CAUTION_WALLET" ORDER BY last_updated DESC',function (err, rows, fields) {
        if (err) {
            console.log(err);
            cb(err, false)
        } else {
            var balance=Number(rows[0].balance);
            console.log(balance)
            if(balance < data.amount){
                cb(null,"INSUFFICIENT_BALANCE");
            }else{
                dbconn.query('INSERT INTO tbl_payout_request SET user_id="' + data.userId + '",amount="' + data.amount + '",acc_holder_name="' + data.acc_holder_name + '",status="REQUESTED",acc_number="'+data.acc_number+'",ifsc_no="'+data.ifsc_no+'",description="'+data.description+'"',function (err, rows, fields) {
                    if (err) {
                        console.log(err);
                        cb(err, false)
                    }else{
                        cb(null,data.userId);
                    }
                })
            }
        }
    })
}

/**
 * delete all pre generated otp for a particular UserId
 */
function deleteOtpByUserId(userId,cb){
    dbconn.query('SELECT COUNT(id) counter FROM tbl_otp WHERE user_id="' + userId + '"', function (err, rows, fields) {
        if (!err) {
            //console.log(rows[0].counter)
            if (rows[0].counter > 0) {
                dbconn.query('DELETE FROM tbl_otp WHERE user_id="' + userId + '"', function (err, rows, fields) {
                    if(err){
                        cb(err, "ERROR");
                    }else{
                        cb(null, true);
                    }
                })
            } else {
                cb(null, true);
            }
        } else {
            cb(err, "ERROR");
        }
    })
}
function courierBooking(data, cb) {
    dbconn.query('SELECT * FROM tbl_user WHERE user_id="' + data.user_id + '"', function (err2, rows2, fields2) {
        if (err2) {
            console.error(err2);
            cb(err2, false);
        } else {
            if (rows2.length == 0) {
                cb(null, "NOUSER");
            } else {
                dbconn.query('SELECT * FROM tbl_fare ORDER BY id DESC LIMIT 1', function (err3, rows3, fields3) {
                    if (err3) {
                        console.error(err3);
                        cb(err3, false);
                    } else {
                        var estPrice    =   0;
                        var distinKm 	=	0;
                        var weightinKG	=	0;
                        var valueGoods	=	0;
                        if(parseFloat(data.value)>0){
                            valueGoods	=	parseFloat(data.value);
                        }
                        if(parseFloat(data.estimatedDistance)>0){
                            distinKm	=	parseFloat(data.estimatedDistance);
                        }
                        if(parseFloat(data.weight)>0){
                            weightinKG	=	parseFloat(data.weight);
                        }
                        estPrice 		=	parseFloat(rows3[0].weight_mul)*weightinKG+parseFloat(rows3[0].distance_mul)*distinKm+parseFloat(rows3[0].val_goods_per)*valueGoods;
                        if(parseFloat(rows3[0].min_fare)>estPrice){
                            estPrice 	=	parseFloat(rows3[0].min_fare);
                        }
                        console.log("Total"+estPrice);
                        estPrice=estPrice.toFixed(2);
                        var insertSql   =   "INSERT INTO `tbl_delivery_request` (`user_id`, `value`, `weight`, `weight_unit`, `height`, `width`, `depth`, `dimension_unit`, `courier_mode`, `pic`, `item_type`,`person_name`, `from_lat`,`from_lng`, `to_lat`, `to_lng`, `from_txt`, `to_txt`, `estimatedDistance`, `status`, `estPrice`,`person_mobile`) VALUES ('"+data.user_id+"','"+data.value+"','"+data.weight+"','"+data.weight_unit+"','"+data.height+"','"+data.width+"','"+data.depth+"','"+data.dimension_unit+"','"+data.courier_mode+"','"+data.pic+"','"+data.item_type+"','"+data.person_name+"','"+data.from_lat+"','"+data.from_lng+"','"+data.to_lat+"','"+data.to_lng+"','"+data.from_txt+"','"+data.to_txt+"','"+data.estimatedDistance+"','REQUESTED','"+estPrice+"','"+data.person_mobile+"')";
                        dbconn.query(insertSql, function (err, rows, fields) {
                            if (err) {
                                console.log(err);
                                cb(err, false);
                            } else {
                                var response = {"request_id":rows.insertId,"estimatedPrice":estPrice};
                                cb(null, response);
                            }
                        })
                    }
                });
            }
        }
    })
}
function courierConfirm(data,cb) {
    dbconn.query('UPDATE tbl_delivery_request SET status ="CONFIRMED" where user_id="' + data.userId + '" AND id="'+data.requestId+'"',function (err,updated) {
        if(err){
            console.error(err);
            cb(err, false);
        }else{
            cb(null,true);
        }
    })
}
function addPromoCode(data, cb) {
    dbconn.query("SELECT id FROM `tbl_promocode` WHERE `promocode`='"+data.promocode+"' AND `isDeleted`='0'", function (err2, rows2, fields2) {
        if (err2) {
            console.log(err);
            cb(err2, false);
        } else {
            if (rows2.length > 0) {
                cb(null, "ALREADY_EXISTS");
            } else {
                var insertSql   =   "INSERT INTO `tbl_promocode` (`promocode`, `description`, `forUserType`, `moneyValue`, `startDate`, `expiryDate`, `isDeleted`) VALUES ('"+data.promocode+"','"+data.description+"','"+data.forUserType+"','"+data.moneyValue+"','"+data.startDate+"','"+data.expiryDate+"','0')";
                dbconn.query(insertSql, function (err, rows, fields) {
                    if (err) {
                        console.log(err);
                        cb(err, false);
                    } else {
                        var response = {"id":rows.insertId};
                        cb(null, response);
                    }
                })
            }
        }
    })
}
function getPromoCodeList(data, cb) {
    if(data.forUserType=="1"){
        var whereClause =   " `forUserType`='1' AND isDeleted='0'";
    } else if(data.forUserType=="2"){
        var whereClause =   " `forUserType` IN ('1','2') AND isDeleted='0'";
    } else if(data.forUserType=="3"){
        var whereClause =   " `forUserType` IN ('1','3') AND isDeleted='0'";
    } else{
        var whereClause =   " isDeleted='0'";
    }
    var selectSql   =   "SELECT `id`,`promocode`, `description`, `forUserType`, `moneyValue`,`createdOn`,`startDate`, `expiryDate`, `isDeleted` FROM `tbl_promocode` WHERE "+whereClause;
    dbconn.query(selectSql, function (err, rows, fields) {
        if (err) {
            console.log(err);
            cb(err, false);
        } else {
            if (rows.length == 0) {
                cb(null, "NO_DATA");
            } else {
                var response = rows;
                cb(null, response);
            }
        }
    })
}
function deletePromocode(data,cb) {
    dbconn.query('UPDATE tbl_promocode SET isDeleted="1" WHERE id="'+data.promoId+'"',function (err,deleted) {
        if(err){
            console.error(err);
            cb(err,false);
        }else{
            cb(null,true);
        }
    })
}
function showPayoutRequest(data,cb) {
    var type=data.type;
    if(type=="ALL")
        var query='select payout.user_id,payout.acc_holder_name,payout.acc_number,payout.ifsc_no,payout.description,payout.requsted_time,payout.accepted_time,payout.status,payout.amount,user.username,user.email,user.user_type,user.phone from tbl_payout_request as payout INNER JOIN tbl_user as user ON payout.user_id = user.user_id';
    if(type == "REQUESTED")
        var query='select payout.user_id,payout.acc_holder_name,payout.acc_number,payout.ifsc_no,payout.description,payout.requsted_time,payout.accepted_time,payout.status,payout.amount,user.username,user.email,user.user_type,user.phone from tbl_payout_request as payout INNER JOIN tbl_user as user ON payout.user_id = user.user_id where payout.status = "REQUESTED"';
    dbconn.query(query,function (err,rows, fields) {
        if(err){
            console.error(err)
            cb(err,false)
        }else{
            cb(null,rows);
        }
    })
}
function pay(data,cb) {
    dbconn.query('SELECT COUNT(id) counter,person_mobile FROM tbl_delivery_request WHERE user_id="' + data.userId + '" AND id ="'+data.orderId+'"',function (err, rows, fields) {
        if(err){
            console.error(err)
            cb(err,false);
        }else{
            if(rows[0].counter > 0){
                var phoneNo=rows[0].person_mobile;
                dbconn.query('SELECT balance from tbl_userpayment where user_id="' + data.userId + '" AND status="SUCCESS" ORDER BY last_updated DESC LIMIT 1', function (err, rows, fields) {
                    if (err) {
                        console.error(err)
                        cb(err, false);
                    } else {
                        var balance = parseFloat(rows[0].balance);
                        if(data.gatewayTransationId === undefined || data.gatewayTransationId === null) {
                            console.log("Wallet Payment");
                            if (balance > data.finalAmount) {
                                function debit(callback) {
                                    var newBalance = balance - data.finalAmount;
                                    // console.log('INSERT INTO tbl_userpayment SET user_id="' + data.userId + '",amount="' + data.finalAmount + '",orderId="' + data.orderId + '",discountAmount="' + data.discountAmount + '",promocode="' + data.promocode + '",balance="' + newBalance + '",is_incoming="0",status="SUCCESS",payment_type="CAUTION_WALLET",operation="PAID"');
                                    dbconn.query('INSERT INTO tbl_userpayment SET user_id="' + data.userId + '",amount="' + data.finalAmount + '",orderId="' + data.orderId + '",discountAmount="' + data.discountAmount + '",promocode="' + data.promocode + '",balance="' + newBalance + '",is_incoming="0",status="SUCCESS",payment_type="CAUTION_WALLET",operation="PAID"', function (err1, rows1, fields1) {

                                        if (err) {
                                            console.error(err)
                                            callback(err, false);
                                        } else {
                                            var txnid = utils.generateTxn() + rows1.insertId;
                                            dbconn.query('UPDATE tbl_userpayment SET txnId="' + txnid + '" WHERE id="' + rows1.insertId + '" ', function (err3, rows3, fields3) {
                                                if (err3) {
                                                    callback(err, false);
                                                } else {
                                                    console.log("DEBITED");
                                                    callback(null, "DEBITED");
                                                }
                                            })
                                        }
                                    });
                                }

                                function credit(callback) {
                                    dbconn.query('SELECT balance from tbl_userpayment where user_id="1" AND status = "SUCCESS" ORDER BY last_updated DESC LIMIT 1', function (err, rows, fields) {
                                        if (err) {
                                            console.error(err)
                                            callback(err, false);
                                        } else {
                                            var balance = parseFloat(rows[0].balance);
                                            var newBalance = balance + data.finalAmount;
                                            dbconn.query('INSERT INTO tbl_userpayment SET user_id="1",amount="' + data.finalAmount + '",orderId="' + data.orderId + '",discountAmount="' + data.discountAmount + '",promocode="' + data.promocode + '",balance="' + newBalance + '",is_incoming="1",status="SUCCESS",payment_type="CAUTION_WALLET",operation="RECEIVED"', function (err1, rows1, fields1) {
                                                if (err) {
                                                    console.error(err)
                                                    callback(err, false);
                                                } else {
                                                    var txnid = utils.generateTxn() + rows1.insertId;
                                                    dbconn.query('UPDATE tbl_userpayment SET txnId="' + txnid + '" WHERE id="' + rows1.insertId + '" ', function (err3, rows3, fields3) {
                                                        if (err3) {
                                                            callback(err, false);
                                                        } else {
                                                            console.log("CREDITED");
                                                            callback(null, "CREDITED");
                                                        }
                                                    })
                                                }
                                            });
                                        }
                                    })
                                }

                                async.parallel([debit, credit], function (err, results) {
                                    if (err) {
                                        console.error(err);
                                        cb(err, false);
                                    } else {
                                        console.log("Payment Done");
                                        data.phoneNo=phoneNo;
                                        confirmationOTP(data,function (err,updated) {
                                            if (err) {
                                                console.error(err);
                                                cb(err, false);
                                            }else{
                                                cb(null, phoneNo);
                                            }
                                        });
                                    }
                                })
                            } else {
                                cb(null, "INSUFFICIENT_BALANCE");
                            }
                        }
                        else{
                            console.log("Gateway Payment");
                            dbconn.query('INSERT INTO tbl_userpayment SET user_id="' + data.userId + '",txnId="' + data.gatewayTransationId + '",amount="' + data.finalAmount + '",orderId="' + data.orderId + '",discountAmount="' + data.discountAmount + '",promocode="' + data.promocode + '",balance="' + balance + '",is_incoming="0",status="SUCCESS",payment_type="CAUTION_WALLET",operation="PAID"', function (err, rows3, fields3) {
                                if (err) {
                                    console.error(err);
                                    cb(err, false);
                                } else {

                                    dbconn.query('SELECT balance from tbl_userpayment where user_id="1" AND status = "SUCCESS" ORDER BY last_updated DESC LIMIT 1', function (err0, rows0, fields0) {
                                        if (err) {
                                            console.error(err0)
                                            cb(err0, false);
                                        } else {
                                            var balance = parseFloat(rows0[0].balance);
                                            var newBalance = balance + data.finalAmount;
                                            dbconn.query('INSERT INTO tbl_userpayment SET user_id="1",amount="' + data.finalAmount + '",orderId="' + data.orderId + '",discountAmount="' + data.discountAmount + '",promocode="' + data.promocode + '",balance="' + newBalance + '",is_incoming="1",status="SUCCESS",payment_type="CAUTION_WALLET",operation="RECEIVED"', function (err1, rows1, fields1) {
                                                if (err) {
                                                    console.error(err1)
                                                    cb(err1, false);
                                                } else {
                                                    var txnid = utils.generateTxn() + rows1.insertId;
                                                    dbconn.query('UPDATE tbl_userpayment SET txnId="' + txnid + '" WHERE id="' + rows1.insertId + '" ', function (err3, rows3, fields3) {
                                                        if (err3) {
                                                            cb(err3, false);
                                                        } else {
                                                            console.log("CREDITED");
                                                            data.phoneNo=phoneNo;
                                                            confirmationOTP(data,function (err,updated) {
                                                                if (err) {
                                                                    console.error(err);
                                                                    cb(err, false);
                                                                }else{
                                                                    cb(null, phoneNo);
                                                                }
                                                            });
                                                        }
                                                    })
                                                }
                                            });
                                        }
                                    })

                                }
                            })
                        }
                    }
                });

            }else{
                cb(null,"NO_ORDER_FOR_THIS_USER");
            }
        }
    });
}
function validatePromoCode(data, cb) {
    dbconn.query("SELECT `user_id`,`status`,`user_type` FROM `tbl_user` WHERE `user_id`='" + data.userId + "' AND `is_deleted`='0'", function (err0, rows0, fields0) {
        if (err0) {
            console.log(err0);
            cb(err0, false);
        } else {
            if (rows0.length == 0) {
                console.log("No User Found");
                cb(null, "NO_USER");
            } else if (rows0[0].status == "0") {
                console.log("No User Found");
                cb(null, "NOTVERIFIED_USER");
            } else {
                dbconn.query("SELECT id,IF(now() BETWEEN startDate AND expiryDate,'0','1') isExpired,moneyValue  FROM `tbl_promocode` WHERE `promocode`='" + data.promocode + "' AND `isDeleted`='0' AND `forUserType` IN ('1','" + rows0[0].user_type + "')", function (err1, rows1, fields1) {
                    if (err1) {
                        console.log(err1);
                        cb(err1, false);
                    } else {
                        if (rows1.length == 0) {
                            cb(null, "NOT_VALID_PROMO");
                        } else if (rows1[0].isExpired == "1") {
                            cb(null, "EXPIRED_PROMO");
                        } else {
                            var exSql = "SELECT `id` FROM `tbl_user_promocode` WHERE user_id='" + data.userId + "' AND `promocode`='" + data.promocode + "'";
                            console.log(exSql);
                            dbconn.query(exSql, function (err2, rows2, fields2) {
                                if (err2) {
                                    console.log("err");
                                    console.log(err2);
                                    cb(err2, false);
                                } else {
                                    // console.log(JSON.stringify(data))
                                    if (rows2.length == 0) {
                                        var insertSql = "INSERT INTO `tbl_user_promocode`(`user_id`, `promo_id`, `promocode`, `status`) VALUES ('" + data.userId + "','" + rows1[0].id + "','" + data.promocode + "','SUCCESS')";
                                        dbconn.query(insertSql, function (err3, rows3, fields3) {
                                            if (err3) {
                                                console.log(err3);
                                                cb(err3, false);
                                            } else {
                                                var response = {
                                                    "id": rows3.insertId,
                                                    "promo_id": rows1[0].id,
                                                    "discountAmount": rows1[0].moneyValue
                                                };
                                                cb(null, response);
                                            }
                                        });
                                    } else {
                                        cb(null, "ALREADY_APPLIED");
                                    }
                                }
                            })
                        }
                    }
                })
            }
        }
    });
}
function cancelDelivery(data,cb) {
   dbconn.query('UPDATE tbl_delivery_request set status="'+data.status+'",reason="'+data.reason+'" where user_id="'+data.userId+'" AND id="'+data.orderId+'"',function (err, rows, fields) {
       if(err){
           console.error(err);
           cb(err,false);
       }else{
           cb(null,true);
       }
   })
}
function updateDeliveryStatus(data,cb) {
    dbconn.query('UPDATE tbl_delivery_request set status="'+data.status+'",driver_id="'+data.userId+'" where id="'+data.orderId+'"',function (err, rows, fields) {
        if(err){
            console.error(err);
            cb(err,false);
        }else{
            cb(null,true);
        }
    })
}
function blockUser(data,cb){
    var isBlocked=data.isBlocked;
    dbconn.query('UPDATE tbl_user set is_blocked="'+isBlocked+'" WHERE user_id="'+data.userId+'"',function (err, rows) {
        if(err){
            console.error(err);
            cb(err,false);
        }else{
            cb(null,true);
        }
    })
}
function searchUser(data,cb) {
    var query="select * from tbl_user where ";
    var counter=0;
    if(data.username){
        counter++;
        query+='username like "%'+data.username+'%" ';
    }
    if(data.email){
        if(counter > 0)
            query+='AND ';
        counter++;
        query+='email like "%'+data.email+'%" ';
    }
    if(data.phone){
        if(counter > 0)
            query+='AND ';
        counter++;
        query+='CONCAT(countryCode,phone) like "%'+data.phone+'%" ';
    }
    if(counter === 0)
        query+='1 ';
    if(data.offset !== undefined && data.limit !== undefined) {
        query += "LIMIT " + data.offset + "," + data.limit;
    }
    else if(data.offset === undefined && data.limit !== undefined) {
        query += 'LIMIT ' + data.limit;
    }
    else{
        query+="LIMIT 100 ";
    }
    console.log(query);
    dbconn.query(query,function (err,rows, fields) {
        if(err){
            console.error(err);
            cb(err,false);
        }else{
            if(rows.length > 0){
                cb(null,rows);
            }else{
                cb(null,"NO_DATA");
            }
        }
    })
}
function userRating(data,cb){
    if(data.usertype === "2")
        var type="driverRatings";
    else
        var type="customerRatings";
    dbconn.query('UPDATE tbl_delivery_request set '+type+'="'+data.ratings+'" WHERE id="'+data.orderId+'"',function (err,updated) {
       if(err){
           console.error(err);
           cb(err,false);
       }else{
           cb(null,true);
       }
   })
}
function checkIsBlocked(data,cb) {
    dbconn.query('SELECT is_blocked from tbl_user WHERE user_id="'+data.userId+'"',function(err,rows,field){
        if(err){
            console.error(err);
            cb(err,'ERROR');
        }else{
            var isblocked=rows[0].is_blocked;
            if(isblocked==="1")
                cb(null,true);
            else
                cb(null,false);
        }
    })
}
function calculateIncentive(data,cb) {
    dbconn.query('SELECT * FROM tbl_user WHERE user_id="' + data.userId + '" AND `is_deleted`="0" AND `status`="1"', function (err2, rows2, fields2) {
        if (err2) {
            cb(err2, false);
        } else {
            if (rows2.length == 0) {
                cb(null, "NOUSER");
            } else {
                dbconn.query('SELECT * FROM `tbl_delivery_request` WHERE `id`="'+data.orderId+'"', function (err5, rows5, fields5){
                    if (err5) {
                        console.log(err5);
                        cb(err5, false);
                    } else {
                        if (rows5.length == 0) {
                            cb(null, "NO_ORDER");
                        } else {
                            var phoneNo=rows5[0].person_mobile;
                            dbconn.query('SELECT * FROM tbl_fare ORDER BY id DESC LIMIT 1', function (err6, rows6, fields6) {
                                if(err6){
                                    console.log(err6);
                                    cb(err6, false);
                                } else {
                                    var amount        =    rows5[0].estPrice;
                                    var taxMul        =    rows6[0].tax_mul;
                                    var dpSharePer        =    rows6[0].dp_share;
                                    var adminSharePer    =    rows6[0].dnow_share;
                                    var taxShare        =    (1-taxMul)*amount;
                                    var dpShare        =    (amount*taxMul)*dpSharePer/100;
                                    var otherAdminShare    =    taxShare+dpShare;
                                    dbconn.query('SELECT balance from tbl_userpayment where user_id="1" AND status = "SUCCESS" ORDER BY last_updated DESC LIMIT 1', function (err7, rows7, fields7) {
                                        if(err7){
                                            console.log(err7);
                                            cb(err7, false);
                                        } else{
                                            var adminBal    =    rows7[0].balance - otherAdminShare;
                                            dbconn.query('INSERT INTO tbl_userpayment SET user_id="1",amount="' + otherAdminShare + '",orderId="' + data.orderId + '",discountAmount="0",promocode="",balance="' + adminBal + '",is_incoming="0",status="SUCCESS",payment_type="CAUTION_WALLET",operation="PAID"', function (err8, rows8, fields8) {
                                                if(err8){
                                                    console.log(err8);
                                                    cb(err8, false);
                                                } else{
                                                    dbconn.query('SELECT balance from tbl_userpayment where user_id="0" AND status = "SUCCESS" ORDER BY last_updated DESC LIMIT 1', function (err9, rows9, fields9) {
                                                        if(err9){
                                                            console.log(err9);
                                                            cb(err9, false);
                                                        } else {
                                                            if(rows9.length>0){
                                                                var taxbal    =    rows9[0].balance + taxShare;
                                                            } else {
                                                                var taxbal    =    taxShare;
                                                            }
                                                            dbconn.query('INSERT INTO tbl_userpayment SET user_id="0",amount="' + taxShare + '",orderId="' + data.orderId + '",discountAmount="0",promocode="",balance="' + taxbal + '",is_incoming="1",status="SUCCESS",payment_type="CAUTION_WALLET",operation="RECEIVED"', function (err10, rows10, fields10) {
                                                                if(err10){
                                                                    console.log(err10);
                                                                    cb(err10, false);
                                                                } else {
                                                                    dbconn.query('SELECT balance from tbl_userpayment where user_id="' + data.userId + '" AND status = "SUCCESS" ORDER BY last_updated DESC LIMIT 1', function (err11, rows11, fields11) {
                                                                        if(err11){
                                                                            console.log(err11);
                                                                            cb(err11, false);
                                                                        } else {
                                                                            if(rows11.length>0){
                                                                                var dpBal    =    rows11[0].balance + dpShare;
                                                                            } else {
                                                                                var dpBal    =    dpShare;
                                                                            }
                                                                            dbconn.query('INSERT INTO tbl_userpayment SET user_id="' + data.userId + '",amount="' + dpShare + '",orderId="' + data.orderId + '",discountAmount="0",promocode="",balance="' + dpBal + '",is_incoming="1",status="SUCCESS",payment_type="CAUTION_WALLET",operation="RECEIVED"', function (err12, rows12, fields12) {
                                                                                if(err12){
                                                                                    console.log(err12);
                                                                                    cb(err12, false);
                                                                                } else {
                                                                                    cb(null, {value:"OK",phoneNo:phoneNo,share:dpShare});
                                                                                }
                                                                            });

                                                                        }
                                                                    });
                                                                }
                                                            });
                                                        }
                                                    });
                                                }
                                            });

                                        }
                                    });

                                }
                            });
                        }
                    }
                });
            }
        }
    })
}
function orderPaymentDetails(data,cb) {
    dbconn.query('SELECT orderId,discountAmount,amount from tbl_userpayment where user_id="'+data.userId+'" AND operation="PAID"',function (err,rows,fields) {
        if(err){
            console.error(err);
            cb(err,"ERROR");
        }else{
            console.log(JSON.stringify(rows));
            cb(null,rows);
        }
    })
}
function confirmationOTP(data,cb) {
    dbconn.query('INSERT into tbl_confirmation_otp SET order_id="'+data.orderId+'",otp="'+data.otp+'",used="0"',function (err,rows,field) {
        if(err){
            console.error(err);
            cb(err,"ERROR");
        }else{
            cb(null,true);
        }
    })
}
function matchConfirmationOTP(data,cb) {
    dbconn.query('SELECT * from tbl_confirmation_otp WHERE order_id="'+data.orderId+'" AND otp="'+data.otp+'"  AND used="0"',function (err,rows) {
        if(err){
            console.error(err);
            cb(err,"ERROR");
        }else{
            if(rows.length > 0){
                dbconn.query('UPDATE tbl_confirmation_otp SET used ="1" WHERE order_id="'+data.orderId+'"AND otp="'+data.otp+'"',function (err,rows) {
                    if(err){
                        console.error(err);
                        cb(err,"ERROR");
                    }else{
                        cb(null,true);
                    }
                })
            }else{
                cb(null,"NOT_MATCHED");
            }
        }
    })
}
function getDashboard(cb){
    function userCount(callback) {
        dbconn.query('SELECT COUNT(user_id) counter FROM tbl_user WHERE is_blocked="0" AND user_type="3"',function (err,rows) {
            if(err){
                console.error(err);
                callback(err,"ERROR");
            }else{
                callback(null,rows[0].counter);
            }
        })
    }
    function driverCount(callback) {
        dbconn.query('SELECT COUNT(user_id) counter FROM tbl_user WHERE is_blocked="0" AND user_type="2"',function (err,rows) {
            if(err){
                console.error(err);
                callback(err,"ERROR");
            }else{
                callback(null,rows[0].counter);
            }
        })
    }
    function bookingCount(callback){
        dbconn.query('SELECT COUNT(id) counter FROM tbl_delivery_request',function (err,rows) {
            if(err){
                console.error(err);
                callback(err,"ERROR");
            }else{
                callback(null,rows[0].counter);
            }
        });
    }
    function currentTripCount(callback) {
        dbconn.query('SELECT COUNT(id) counter FROM tbl_delivery_request WHERE status="ON_TRIP"',function (err,rows) {
            if(err){
                console.error(err);
                callback(err,"ERROR");
            }else{
                callback(null,rows[0].counter);
            }
        });
    }
    function payoutRequestCount(callback) {
        dbconn.query('SELECT COUNT(id) counter FROM tbl_payout_request WHERE status="REQUESTED"',function (err,rows) {
            if(err){
                console.error(err);
                callback(err,"ERROR");
            }else{
                callback(null,rows[0].counter);
            }
        });
    }
    function disputeCaseCount(callback) {
        dbconn.query('SELECT COUNT(id) counter FROM tbl_userpayment WHERE payment_type="DISPUTE_WALLET"',function (err,rows) {
            if(err){
                console.error(err);
                callback(err,"ERROR");
            }else{
                callback(null,rows[0].counter);
            }
        });
    }
    function activePromocodeCount(callback){
        dbconn.query('SELECT COUNT(id) counter FROM tbl_promocode WHERE isDeleted="0" AND NOW() BETWEEN startDate AND expiryDate',function (err,rows) {
            if(err){
                console.error(err);
                callback(err,"ERROR");
            }else{
                callback(null,rows[0].counter);
            }
        });
    }
    function companyWallet(callback){
        dbconn.query('SELECT balance FROM tbl_userpayment WHERE user_id="1" AND payment_type="CAUTION_WALLET" AND status="SUCCESS" ORDER BY last_updated DESC LIMIT 1',function (err,rows) {
            if(err){
                console.error(err);
                callback(err,"ERROR");
            }else{
                callback(null,rows[0].balance);
            }
        })
    }
    function suspenseWallet(callback) {
        dbconn.query('SELECT balance FROM tbl_userpayment WHERE user_id="1" AND payment_type="DISPUTE_WALLET" AND status="SUCCESS" ORDER BY last_updated DESC LIMIT 1',function (err,rows) {
            if(err){
                console.error(err);
                callback(err,"ERROR");
            }else{
                if(rows.length >0)
                    callback(null,rows[0].balance);
                else
                    callback(null,0);
            }
        })
    }
    async.parallel([userCount,driverCount,bookingCount,currentTripCount,payoutRequestCount,disputeCaseCount,activePromocodeCount,companyWallet,suspenseWallet],function (err,results) {
        if(err){
            cb(err,"ERROR");
        }else{
            var data={"users":results[0],drivers:results[1],booking:results[2],currentTrip:results[3],payout:results[4],dispute:results[5],promo:results[6],cWallet:results[7],sWallet:results[8]};
            cb(null,data);
        }
    })
}
function resendConfirmationOtp(data,cb) {
   dbconn.query('UPDATE tbl_confirmation_otp SET otp="'+data.otp+'" WHERE order_id="'+data.orderId+'"',function (err,rows) {
       if(err){
           console.error(err);
           cb(err,false);
       }else{
          cb(null,true);
       }
   })
}



module.exports.resendConfirmationOtp        =   resendConfirmationOtp;
module.exports.getDashboard        =   getDashboard;
module.exports.matchConfirmationOTP        =   matchConfirmationOTP;
module.exports.confirmationOTP        =   confirmationOTP;
module.exports.orderPaymentDetails        =   orderPaymentDetails;
module.exports.checkIsBlocked        =   checkIsBlocked;
module.exports.calculateIncentive        =   calculateIncentive;
module.exports.userRating        =   userRating;
module.exports.searchUser        =   searchUser;
module.exports.blockUser        =   blockUser;
module.exports.updateDeliveryStatus        =   updateDeliveryStatus;
module.exports.cancelDelivery        =   cancelDelivery;
module.exports.validatePromoCode        =   validatePromoCode;
module.exports.pay         =   pay;
module.exports.showPayoutRequest         =   showPayoutRequest;
module.exports.deletePromocode         =   deletePromocode;
module.exports.courierConfirm         =   courierConfirm;
module.exports.getPromoCodeList         =   getPromoCodeList;
module.exports.addPromoCode             =   addPromoCode;
module.exports.payoutMoney              =   payoutMoney;
module.exports.signUp                   =   signUp;
module.exports.signIn                   =   signIn;
module.exports.forgotPassword           =   forgotPassword;
module.exports.resetPassword            =   resetPassword;
module.exports.changePassword           =   changePassword;
module.exports.verifyUser               =   verifyUser;
module.exports.getProfile               =   getProfile;
module.exports.editProfile              =   editProfile;
module.exports.addBankDetails           =   addBankDetails;
module.exports.cautionMoneyDeposit      =   cautionMoneyDeposit;
module.exports.walletBalance            =   walletBalance;
module.exports.getTransactionHistory    =   getTransactionHistory;
module.exports.courierBooking           =   courierBooking;
