# -*- coding: utf-8 -*-

import tornado.ioloop
import tornado.web
import tornado.websocket
import urllib.parse
import urllib.request


html_escape_table = { "&": "&amp;", '"': "&quot;", "'": "&apos;", ">": "&gt;", "<": "&lt;" }
rdbms = ""

def html_escape(text):
    """Produce entities within text."""
    global html_escape_table
    return "".join(html_escape_table.get(c,c) for c in text)



class Log:
    def log(self, message):
        f = open('logFile', 'a')
        f.write("{0}\n".format(message))
        f.close()
        print("Logger: {0}".format(message))

logger = Log()
clients = []

class ChatClient(tornado.websocket.WebSocketHandler):
    def open(self):
        self.username = 'user'
        self.clan = ''
        self.initialized = False
        global logger
        clients.append(self)

    def on_message(self, message):
        try:
            message = html_escape(message)
            if self.initialized == True:
                if message.startswith("/clan "):
                    self.clanMessageReceived(message)
                elif message.startswith("/w ") or message.startswith("/whisper "):
                    self.whisperMessageReceived(message)
                else:
                    self.gameMessageReceived(message)
            else:
                self.initializeChatUser(message)
        except Exception as e:
            global logger
            logger.log(e)
            self.close()

    def initializeChatUser(self, message):
        token = message.strip()
        if token == None or token == '':
            raise Exception("No token given")
        url = 'http://127.0.0.1/en/validateChatToken'
        values = {'token': token}
        data = urllib.parse.urlencode(values)
        data = data.encode('utf-8')
        req = urllib.request.Request(url, data)
        response = urllib.request.urlopen(req)
        page = str(response.read().decode('utf-8'))
        results = page.split(' ');
        if results[0] == 'KO':
            raise Exception("Invalid token")
        self.username = results[1]
        if results[2] == 'NOCLAN':
            self.clan = ''
        else:
            self.clan = '[{0}]'.format(results[2])
        self.gameID = results[3]
        gameName = results[4]
        self.loc = results[5]
        self.initialized = True
        if self.loc != 'fr':
            msg = 'Game {0} !'.format(gameName)
        else:
            msg = 'Partie {0} !'.format(gameName)
        self.write_message(msg)

    def on_close(self):
        global clients
        for client in clients:
            if client == self:
                print("Client left.")
                clients.remove(client)
                return

    def gameMessageReceived(self, message):
        global clients
        for client in clients:
            if client.gameID == self.gameID:
                client.write_message(u"{0}{1}: {2}".format(self.clan, self.username, message))

    def whisperMessageReceived(self, message):
        global clients
        tokens = message.split(' ')
        if len(tokens) < 3:
            self.write_message(u"Usage: '/w [contact] [message]'")
            return
        target = tokens[1]
        offset = len(target) + 1
        msg = message[message.index(target)+offset:]
        for client in clients:
            if client.username == target:
                client.write_message(u"<span class='whisper'>{0}{1}: {2}</span>".format(self.clan, self.username, msg))
                return

    def clanMessageReceived(self, message):
        if self.clan == '':
            return
        global clients
        offset = len("/clan ")
        msg = message[offset:]
        for client in clients:
            if client.clan == self.clan:
                client.write_message(u"/clan <span class='clan'>{0}: {1}</span>".format(self.username, msg))

application = tornado.web.Application([
    (r"/", ChatClient),
])

if __name__ == "__main__":
    try:
        application.listen(4344)
        tornado.ioloop.IOLoop.instance().start()
    except Exception:
        application.close()





