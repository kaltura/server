package lib.Kaltura.notification;

/**
 * This enum indicates all the time-based events 
 */
public enum HandlerProcessType {

        // Handler is always executed whatever the notification type is
		PROCESS, 
        // handler is executed prior to other handlers
        PRE_PROCESS,
        // handler is executed after other handlers
        POST_PROCESS;
        
}