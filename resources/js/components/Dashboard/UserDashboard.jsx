import { useState } from "react";
import Navbar from "../Layout/Navbar";
import UserSidebar from "../Layout/UserSidebar.jsx";
import UserBorrowing from "./UserBorrowing.jsx";

export default function UserDashboard() {
    const [activeTab, setActiveTab] = useState("borrowing");

    const renderTab = () => {
        switch(activeTab) {
            case "borrowing": return <UserBorrowing />;
            default: return <UserBorrowing />;
        }
    };

    return (
        <div className="flex min-h-screen bg-neutral-300">
            <UserSidebar activeTab={activeTab} setActiveTab={setActiveTab}/>
            <div className="flex-1 flex flex-col">
                <Navbar/>
                <main className="flex-1 p-8">{renderTab()}</main>
            </div>
        </div>
    );
}
