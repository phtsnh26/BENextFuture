import { Server } from "socket.io";

const io = new Server({
    cors: {
        origin: "http://localhost:5173",
    }
});

let onlineUsers = [];
const addNewUser = (data, socketId) => {
    const {id} = data
    !onlineUsers.some(user=>user.id === id) && onlineUsers.push({id, socketId})
}

io.on("connection", (socket) => {
    const userAgents = socket.handshake.headers["user-agent"];
    console.log("someone has connected using: ", userAgents);

    io.emit("welcome", "Welcome to the server");

    socket.on('newUser', (data) => {
        addNewUser(data, socket.id)
    });

    socket.on("disconnect", () => {
        console.log("someone has left the server");
    })
})

io.listen(3001);