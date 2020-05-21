const TelegramBot = require('node-telegram-bot-api')
const config = require('./config')
const helper = require('./helper')
const keyboard = require('./keyboard')
const kb = require('./keyboard-buttons')
const ikb = require('./inline-keyboard')
const fs = require('fs')
const fetch = require('node-fetch')
const mysql = require('mysql')

//Bot has been started
helper.logStart()
const bot = new TelegramBot(config.TOKEN,{
    polling:true
})


bot.onText(/\/start/,(msg)=>{
    
    /*
    console.log('user_id: '+msg.from.id)`
    console.log('username: @'+msg.from.username)
    console.log('first_name: '+msg.from.first_name)
    console.log('last_name: '+msg.from.last_name)
    */
   var connection = mysql.createConnection({
     host     : 'localhost',
     user     : 'root',
     password : 'root',
     database : 'oscar'
   });
    
   connection.connect();
   connection.query(`REPLACE client (chat_id) VALUES(${msg.from.id})`,(err,results,fields)=>{
        if(err) console.log('error');
    })
   connection.end()

    const text = `Добро пожаловать на наш магазин ${msg.from.first_name}\nВыберетье команду:`
    bot.sendMessage(helper.getChatId(msg),text,{
        reply_markup:{
            keyboard:keyboard.main,
            resize_keyboard:true
        }
    })

    
})



bot.on('message',(msg)=>{
    const chatId = helper.getChatId(msg)


    switch(msg.text){
        case kb.main.catalogues:
            //console.log('katalog'+msg.message_id)
            
                bot.sendMessage(chatId,'Наш каталог',{
                    reply_markup:{
                        keyboard: keyboard.exit,
                        resize_keyboard:true
                    }
                }).then(()=>{
                    //buyoda catalogues dgan object fetch qiladi list of lists ni
                    fetch('http://oscar/category/get')
                    .then(response => response.json())
                    .then(data=>{
                        var send_to_root=key_value_pairs(data.data)
                        //console.log(send_to_root)
                        bot.sendMessage(chatId,'Выберите один из каталогов:',{
                            reply_markup:{
                                inline_keyboard:send_to_root
                            }
                        })
                        //console.log(send_to_root)
                    })
                    
                })
           

            
            
            break


        case kb.main.sale:
            //console.log('sale')
            
            fetch('http://oscar/sale/all')
            .then(response => response.json())
            .then(data => {
                if(data==''){
                    console.log('empty')
                    bot.sendMessage(chatId,'⚠️ Извините, на данный момент нет никаких акций 😔',{
                        reply_markup:{
                            keyboard:keyboard.exitabout,
                            resize_keyboard:true
                        } 
                    })
                }else{
                    bot.sendMessage(chatId,'🔔 Акция на OSCAR MEBEL',{
                        reply_markup:{
                            keyboard:keyboard.exitabout,
                            resize_keyboard:true
                        }
                    })
                    bot.sendChatAction(chatId,'upload_photo')
                    .then(()=>{
                        var datas = data
                        datas.forEach((data)=>{
                            //10 charecterni bowidigini kesib tawimz
                            bot.sendChatAction(chatId,'upload_photo')
                            .then(()=>{
                                bot.sendPhoto(chatId,'.'+data.img.substr(10,data.img.length))
                            }) 
                        })
                        
                    })

                }
                
                
             })
            .catch(err => {console(err)})
            break
            

        case kb.main.about:
            console.log('about')
            const title = config.about
            bot.sendLocation(chatId,41.354796,69.253512)
            .then(()=>{
                bot.sendMessage(chatId,title,{
                    reply_markup:{
                        keyboard:keyboard.exitabout,
                        resize_keyboard:true
                    },
                    parse_mode:'HTML'
                })
            })
            break
       

        case kb.exitabout.back:
            const message1 = `Выберитье раздел:`
            bot.sendMessage(helper.getChatId(msg),message1,{
                reply_markup:{
                    keyboard:keyboard.main,
                    resize_keyboard:true
                }
            })
            break



        case kb.exit.mcatalogue:
            
            fetch('http://oscar/category/get')
            .then(response => response.json())
            .then(data=>{
                var send_to_root=key_value_pairs(data.data)
                //console.log(send_to_root)
                bot.sendMessage(chatId,'Выберите один из каталогов:',{
                    reply_markup:{
                        inline_keyboard:send_to_root
                    }
                })
            })
            
            break



        case kb.exit.exit:
            const message = `Выберитье раздел:`
            bot.sendMessage(helper.getChatId(msg),message,{
                reply_markup:{
                    keyboard:keyboard.main,
                    resize_keyboard:true
                }
            })
            break
    }
})
bot.on('callback_query',query=>{
    var status,sub_category,callback_data
    //console.log(query.data)
    fetch(`http://oscar/category/get?id=${query.data}`)
        .then(response => response.json())
        .then(data=>{
            //console.log(send_to_root)
            status = data.status //0 -keyboard yoki 1-good
            sub_category = key_value_pairs(data.data) //keyobard
            //callback_data = query.data  //callback_data bu id shuni id ga berish kk
            
            //console.log(data.parent)
            if(data.parent!=null){
                //console.log('parent not NULL')
                sub_category.push([{text:'↖️ Вернуться в суб-каталог',callback_data:data.parent}])
            }
            //console.log(sub_category)

            //keyboard holati uchun
        if(status==0){
            //console.log('status 0')
            bot.deleteMessage(query.message.chat.id,query.message.message_id)
            .then(()=>{
                bot.sendMessage(query.message.chat.id,'Выберите раздел чтобы вывести список товаров:',{
                    reply_markup:{
                        inline_keyboard:sub_category, //shuyoga api digi DATA ni assign
                    }
                })
                
            })
            
            


        }
        //data holati uchun
        else if(status==1){
            
                var goods = data.data
                goods.forEach(good=>{
                    bot.sendChatAction(query.message.chat.id,'upload_photo')
                    .then(()=>{
                        bot.sendPhoto(query.message.chat.id,'.'+good.img.substr(10,good.img.length),{
                            caption: good.description,
                            reply_markup:{
                                inline_keyboard:[
                                    [{text:'Для информации',url:'https://t.me/Oscarofficefurniture_bot'}]
                                ]
                            }
                        })
                    })
                })

            
            
        }
        else{
            console.log('status is not either 0 or 1')
            bot.sendMessage(query.message.chat.id,'Извините, эта категория пока пуста!')
        }
            


        })
        
        




})


/*OLd one
bot.on('callback_query',query=>{
    //agar sub-category bosa inline-KEYBOARD jonatadigan holat >>>>[1API] dan olidn Fetch qilib
    if(query.data.slice(0,2)=='sc'){
        bot.deleteMessage(query.message.chat.id,query.message.message_id)
        .then(()=>{
            bot.sendMessage(query.message.chat.id,'Выберите раздел чтобы вывести список товаров:',{
                reply_markup:{
                    inline_keyboard:ikb[query.data], //shuyoga api digi DATA ni assign
                    
                }
            })
        })
    }
    //bu har doim shunaqa root bn boshlanadi
    else if(query.data=='root'){
        
    }
    //agar sc bomasa goods jonatadi boshqa >>>>>>>>>>>>>>>>>>>[2API] dan
    else{
        var goods = ikb[query.data] //shuyoga api digi DATA ni assign qilish
        
            goods.forEach(good=>{
                bot.sendChatAction(query.message.chat.id,'upload_photo')
                .then(()=>{
                    bot.sendPhoto(query.message.chat.id,good.picture,{
                        caption: good.description,
                        reply_markup:{
                            inline_keyboard:[
                                [{text:'Для информации',url:'https://t.me/Oscarofficefurniture_bot'}]
                            ]
                        }
                    })
                })
            })
        }


})

*/



//////////////```Plugins```///////////////////////////
function key_value_pairs(obj) 
   {
    var keys = _keys(obj);
    var length = keys.length;
    var pairs = Array(length);
    for (var i = 0; i < length; i++) 
    {
      pairs[i] = [obj[keys[i]]];
    }
    return pairs;
  }

function _keys(obj) 
  {
    if (!isObject(obj)) return [];
    if (Object.keys) return Object.keys(obj);
    var keys = [];
    for (var key in obj) if (_.has(obj, key)) keys.push(key);
    return keys;
  }
 function isObject(obj) 
 {
    var type = typeof obj;
    return type === 'function' || type === 'object' && !!obj;
  }