import { useAuth } from "../../context/AuthContext";
import { Navigate } from "react-router-dom";
import AdminDashboard from "../../pages/AdminDashboard.jsx";
import UserDashboard from "../../pages/UserDashboard.jsx";

export default function DashboardRouter() {
    const { user } = useAuth();

    if (!user) {
        return <Navigate to="/login" replace />;
    }

    return user.role === "admin" ? <AdminDashboard /> : <UserDashboard />;
}
