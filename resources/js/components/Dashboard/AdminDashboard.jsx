import { useState } from "react";
import Users from "./Users";
import AdminSidebar from "../Layout/AdminSidebar.jsx";
import Navbar from "../Layout/Navbar";
import Books from "./Books.jsx";
import BorrowingSystem from "./BorrowingSystem.jsx";

export default function AdminDashboard() {
    const [activeTab, setActiveTab] = useState("users");

    const renderTab = () => {
        switch(activeTab) {
            case "users":
                return <Users />;
            case "books":
                return <Books />;
            case "borrowing":
                return <BorrowingSystem />;
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
