package app.unsimpledev.appnotificaciones;


import androidx.annotation.NonNull;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

public class MyFirebaseMessagingService extends FirebaseMessagingService {

    private static final String TAG = "MyFirebaseMsgService";

    @Override
    public void onMessageReceived(@NonNull RemoteMessage message) {

        // SI QUIERO PROCESAR DATOS DE LA NOTIFICACION
        //if (message.getData().size() > 0) {
        //    Log.d(TAG, "Message data payload: " + message.getData());
        //}
        //if (message.getNotification() != null) {
        //    Log.d(TAG, "Message Notification Body: " + message.getNotification().getBody());
        //}

        super.onMessageReceived(message);
    }




}
