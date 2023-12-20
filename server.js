import express from 'express'
import http from 'http'
import serverIO from 'socket.io'

const app = express()
const server = http.createServer(app)
const io = serverIO(server)

io.on('connection', (socket) => {
    console.log('New connection')

    socket.on('disconnect', () => {
        console.log('Client disconnected')
    })
})

const port = process.env.PORT || 3000

server.listen(port, () => {
    console.log(`Server is up on port ${port}`)
})