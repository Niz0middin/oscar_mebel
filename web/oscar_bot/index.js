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

    if(msg.text=='aksiya'){
     console.log(msg.text)
    
    bot.deleteMessage(chatId,msg.message_id)
    .then(()=>{
    ////
        fetch('http://oscar/api/sales')
            .then(response => response.json())
            .then(data => {
            	console.log(data.length-1)
                bot.sendMessage(chatId,'Акция на OSCAR MEBEL',{
                    reply_markup:{
                        keyboard:keyboard.exit,
                        resize_keyboard:true
                    }
                })
                bot.sendChatAction(chatId,'upload_photo')
                .then(()=>{
                        //10 charecterni bowidigini kesib tawimz 
                        bot.sendPhoto(chatId,'.'+data[data.length-1].img.substr(10,data[data.length-1].img.length))
                })
                
             })
            .catch(err => {console(err)})
            ///////
    })
    }

    switch(msg.text){
        case kb.main.catalogues:
            console.log('katalog'+msg.message_id)
            
                bot.sendMessage(chatId,'Наш каталог',{
                    reply_markup:{
                        keyboard: keyboard.exit,
                        resize_keyboard:true
                    }
                }).then(()=>{
                    bot.sendMessage(chatId,'Выберите один из каталогов:',{
                        reply_markup:{
                            inline_keyboard:ikb.catalogues
                        }
                    })
                })
           

            
            
            break


        case kb.main.sale:
            console.log('sale')
            
            fetch('http://oscar/api/sales')
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
                        keyboard:keyboard.exit,
                        resize_keyboard:true
                    },
                    parse_mode:'HTML'
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