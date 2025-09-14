// Initialize MongoDB for audit logs
db = db.getSiblingDB('audit_logs_db');

// Create audit_logs collection
db.createCollection('audit_logs');

// Create indexes for better performance
db.audit_logs.createIndex({ "created_at": -1 });
db.audit_logs.createIndex({ "user_id": 1 });
db.audit_logs.createIndex({ "reference_type": 1, "reference_id": 1 });
db.audit_logs.createIndex({ "action": 1 });

// Create a sample document structure
db.audit_logs.insertOne({
    "user_id": 1,
    "reference_type": "Task",
    "reference_id": 1,
    "action": "created",
    "description": "Task 'Sample Task' was created",
    "old_values": null,
    "new_values": {
        "title": "Sample Task",
        "description": "Sample description",
        "status": "pending"
    },
    "ip_address": "127.0.0.1",
    "user_agent": "Mozilla/5.0...",
    "created_at": new Date(),
    "updated_at": new Date()
});

print('MongoDB audit_logs_db initialized successfully!');
