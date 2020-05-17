const TelegramBot = require('node-telegram-bot-api')
const config = require('./config')
const helper = require('./helper')
const keyboard = require('./keyboard')
const kb = require('./keyboard-buttons')
const ikb = require('./inline-keyboard')
const fs = require('fs')
const fetch = require('node-fetch')

//Bot has been started
helper.logStart()
const bot = new TelegramBot(config.TOKEN,{
    polling:true
})


bot.onText(/\/start/,(msg)=>{
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
            console.log('katalog'+msg.message_id)
            
                bot.sendMessage(chatId,'Наш каталог',{
                    reply_markup:{
                        keyboard: keyboard.exit,
                        resize_keyboard:true
                    }
                }).then(()=>{
                    //buyoda catalogues dgan object fetch qiladi list of lists ni
                    bot.sendMessage(chatId,'Выберите один из каталогов:',{
                        reply_markup:{
                            inline_keyboard:ikb.root
                        }
                    })
                })
           

            
            
            break


        case kb.main.sale:
            console.log('sale')
            
            fetch('http://oscar/sale/one?id=4')
            .then(response => response.json())
            .then(data => {
                bot.sendMessage(chatId,'Акция на OSCAR MEBEL',{
                    reply_markup:{
                        keyboard:keyboard.exit,
                        resize_keyboard:true
                    }
                })
                bot.sendChatAction(chatId,'upload_photo')
                .then(()=>{
                    var datas = data
                    datas.forEach((data)=>{
                        //10 charecterni bowidigini kesib tawimz 
                        bot.sendPhoto(chatId,'.'+data.img.substr(10,data.img.length))
                    })
                    
                })
                
             })
            .catch(err => {console(err)})
            break
            

        case kb.main.about:
            console.log('about')
            const title = `  <b>OSCAR Mebel</b>`
            bot.sendLocation(chatId,41.354796,69.253512)
            .then(()=>{
                bot.sendMessage(chatId,title+'\n\n📍 Наш адрес: ____\n⏩ Оринтир:_____\n☎️ Телефон:_____\n📲 Телеграм:@____',{
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
            
                bot.sendMessage(helper.getChatId(msg),'Выберите раздел чтобы вывести список товаров:',{
                    reply_markup:{
                        inline_keyboard:ikb.root 
                    }
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