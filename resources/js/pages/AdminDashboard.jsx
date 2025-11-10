import { useState } from "react";
import Users from "../components/Dashboard/Users.jsx";
import AdminSidebar from "../components/Layout/AdminSidebar.jsx";
import Navbar from "../components/Layout/Navbar.jsx";
import Books from "../components/Dashboard/Books.jsx";
import Borrowing from "../components/Dashboard/Borrowing.jsx";

export default function AdminDashboard() {
    const [activeTab, setActiveTab] = useState("users");

    const renderTab = () => {
        switch(activeTab) {
            case "users":
                return <Users />;
            case "books":
                return <Books />;
            case "borrowing":
                return <Borrowing />;
            default:
                return <Users />;
        }
    };

    return (
        <div className="flex min-h-screen bg-neutral-300">
            <AdminSidebar activeTab={activeTab} setActiveTab={setActiveTab}/>
            <div className="flex-1 flex flex-col">
                <Navbar/>
                <main className="flex-1 p-8">{renderTab()}</main>
            </div>
        </div>
    );
}
