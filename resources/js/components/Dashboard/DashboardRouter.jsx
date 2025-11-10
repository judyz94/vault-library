import { useAuth } from "../../context/AuthContext";
import { Navigate } from "react-router-dom";
import AdminDashboard from "./AdminDashboard";
import UserDashboard from "./UserDashboard";

export default function DashboardRouter() {
    const { user } = useAuth();

    if (!user) {
        return <Navigate to="/login" replace />;
    }

    return user.role === "admin" ? <AdminDashboard /> : <UserDashboard />;
}
