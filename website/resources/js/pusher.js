
import Pusher from "pusher-js";

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;
const pusher = new Pusher(pusherKey, {
    cluster: pusherCluster,
});

export default pusher;

