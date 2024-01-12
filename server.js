import { Server } from "socket.io";

const io = new Server({
    cors: {
        origin: "http://localhost:5173",
    },
});

let onlineUsers = [];

const addNewUser = (data, socketId) => {
    const { id } = data;

    const isNewUser = !onlineUsers.some((user) => user.id === id);

    if (isNewUser) {
        onlineUsers.push({ id, socketId });
    }
};
const getUser = (userId) => {
    return onlineUsers.filter((user) => user.id === userId);
};
const removeUser = (socketId) => {
    onlineUsers = onlineUsers.filter((user) => user.socketId !== socketId);
};
io.on("connection", (socket) => {
    const userAgents = socket.handshake.headers["user-agent"];
    console.log("someone has connected using: ", userAgents);

    io.emit("welcome", "Welcome to the server");

    socket.on("newUser", (data) => {
        addNewUser(data, socket.id);
        console.log(`${data.username} has connected!`);
        console.log(onlineUsers);
        io.emit("onlineUser", onlineUsers);
        io.emit("activeAccounts", onlineUsers.length);
    });

    socket.on("sendNotification", ({ senderId, receiverId, type }) => {
        console.log(
            `senderId: ${senderId}, receiverId: ${receiverId}, type: ${type}`
        );
        const receiver = getUser(receiverId);
        io.to(receiver[0].socketId).emit("getNotification", {
            senderId,
            type,
        });
    });

    socket.on("disconnect", () => {
        removeUser(socket.id);
        io.emit("onlineUser", onlineUsers);
        io.emit("activeAccounts", onlineUsers.length);
        console.log("someone has left the server");
    });
});

io.listen(3001);
