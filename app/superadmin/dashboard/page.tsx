"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Users, UserPlus, Shield, Trophy, Calendar, Settings, LogOut, Menu, Home, Database } from "lucide-react"
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet"

export default function SuperAdminDashboard() {
  const router = useRouter()
  const [loading, setLoading] = useState(true)
  const [stats, setStats] = useState({
    users: 0,
    teams: 0,
    tournaments: 0,
    matches: 0,
  })

  useEffect(() => {
    // Check if user is authenticated as superadmin
    const token = localStorage.getItem("superadmin_token")
    const role = localStorage.getItem("user_role")

    if (!token || role !== "superadmin") {
      router.push("/superadmin/login")
      return
    }

    // Fetch dashboard data
    // This would be an API call in a real application
    setTimeout(() => {
      setStats({
        users: 1250,
        teams: 87,
        tournaments: 12,
        matches: 156,
      })
      setLoading(false)
    }, 1000)
  }, [router])

  const handleLogout = () => {
    localStorage.removeItem("superadmin_token")
    localStorage.removeItem("user_role")
    router.push("/superadmin/login")
  }

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">Loading dashboard...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Mobile Navigation */}
      <Sheet>
        <SheetTrigger asChild>
          <Button variant="ghost" size="icon" className="md:hidden fixed top-4 left-4 z-50">
            <Menu className="h-6 w-6" />
          </Button>
        </SheetTrigger>
        <SheetContent side="left" className="w-64">
          <div className="flex flex-col h-full">
            <div className="py-6 px-2">
              <h2 className="text-xl font-bold mb-6">SportsVani Admin</h2>
              <nav className="space-y-2">
                <NavItem icon={<Home />} label="Dashboard" href="/superadmin/dashboard" />
                <NavItem icon={<Users />} label="Users" href="/superadmin/users" />
                <NavItem icon={<Shield />} label="Teams" href="/superadmin/teams" />
                <NavItem icon={<Trophy />} label="Tournaments" href="/superadmin/tournaments" />
                <NavItem icon={<Calendar />} label="Matches" href="/superadmin/matches" />
                <NavItem icon={<Database />} label="System" href="/superadmin/system" />
                <NavItem icon={<Settings />} label="Settings" href="/superadmin/settings" />
              </nav>
            </div>
            <div className="mt-auto p-4">
              <Button variant="outline" className="w-full justify-start" onClick={handleLogout}>
                <LogOut className="mr-2 h-4 w-4" />
                Logout
              </Button>
            </div>
          </div>
        </SheetContent>
      </Sheet>

      {/* Desktop Sidebar */}
      <div className="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-white shadow-sm">
        <div className="flex-1 flex flex-col min-h-0 border-r border-gray-200">
          <div className="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
            <div className="flex items-center flex-shrink-0 px-4">
              <h1 className="text-xl font-bold">SportsVani Admin</h1>
            </div>
            <nav className="mt-8 flex-1 px-4 space-y-2">
              <NavItem icon={<Home />} label="Dashboard" href="/superadmin/dashboard" />
              <NavItem icon={<Users />} label="Users" href="/superadmin/users" />
              <NavItem icon={<Shield />} label="Teams" href="/superadmin/teams" />
              <NavItem icon={<Trophy />} label="Tournaments" href="/superadmin/tournaments" />
              <NavItem icon={<Calendar />} label="Matches" href="/superadmin/matches" />
              <NavItem icon={<Database />} label="System" href="/superadmin/system" />
              <NavItem icon={<Settings />} label="Settings" href="/superadmin/settings" />
            </nav>
          </div>
          <div className="flex-shrink-0 flex border-t border-gray-200 p-4">
            <Button variant="outline" className="w-full justify-start" onClick={handleLogout}>
              <LogOut className="mr-2 h-4 w-4" />
              Logout
            </Button>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="md:pl-64 flex flex-col flex-1">
        <main className="flex-1">
          <div className="py-6 px-4 sm:px-6 lg:px-8">
            <h1 className="text-2xl font-semibold text-gray-900 mb-6">Super Admin Dashboard</h1>

            {/* Stats Overview */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
              <StatCard
                icon={<Users className="h-8 w-8 text-blue-500" />}
                title="Total Users"
                value={stats.users}
                description="Registered users"
              />
              <StatCard
                icon={<Shield className="h-8 w-8 text-green-500" />}
                title="Teams"
                value={stats.teams}
                description="Active teams"
              />
              <StatCard
                icon={<Trophy className="h-8 w-8 text-amber-500" />}
                title="Tournaments"
                value={stats.tournaments}
                description="Active tournaments"
              />
              <StatCard
                icon={<Calendar className="h-8 w-8 text-purple-500" />}
                title="Matches"
                value={stats.matches}
                description="Total matches"
              />
            </div>

            {/* Dashboard Tabs */}
            <Tabs defaultValue="overview" className="space-y-4">
              <TabsList>
                <TabsTrigger value="overview">Overview</TabsTrigger>
                <TabsTrigger value="users">Users</TabsTrigger>
                <TabsTrigger value="teams">Teams</TabsTrigger>
                <TabsTrigger value="tournaments">Tournaments</TabsTrigger>
              </TabsList>

              <TabsContent value="overview" className="space-y-4">
                <Card>
                  <CardHeader>
                    <CardTitle>Recent Activity</CardTitle>
                    <CardDescription>Overview of recent platform activity</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-4">
                      <ActivityItem
                        icon={<UserPlus className="h-5 w-5 text-green-500" />}
                        title="New User Registration"
                        description="Amit Kumar registered as a Player, Umpire, and Scorer"
                        time="2 hours ago"
                      />
                      <ActivityItem
                        icon={<Shield className="h-5 w-5 text-blue-500" />}
                        title="New Team Created"
                        description="Mumbai Strikers team was created by Rahul Sharma"
                        time="5 hours ago"
                      />
                      <ActivityItem
                        icon={<Trophy className="h-5 w-5 text-amber-500" />}
                        title="Tournament Started"
                        description="Corporate Cricket League 2025 has started"
                        time="1 day ago"
                      />
                      <ActivityItem
                        icon={<Calendar className="h-5 w-5 text-purple-500" />}
                        title="Match Completed"
                        description="Delhi Dragons vs Chennai Challengers match completed"
                        time="1 day ago"
                      />
                    </div>
                  </CardContent>
                </Card>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <Card>
                    <CardHeader>
                      <CardTitle>System Status</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="space-y-2">
                        <StatusItem label="Database" status="Operational" color="green" />
                        <StatusItem label="Firebase Auth" status="Operational" color="green" />
                        <StatusItem label="YouTube API" status="Operational" color="green" />
                        <StatusItem label="Storage" status="Operational" color="green" />
                      </div>
                    </CardContent>
                  </Card>

                  <Card>
                    <CardHeader>
                      <CardTitle>Quick Actions</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="grid grid-cols-2 gap-2">
                        <Button className="w-full">
                          <UserPlus className="mr-2 h-4 w-4" />
                          Add User
                        </Button>
                        <Button className="w-full">
                          <Shield className="mr-2 h-4 w-4" />
                          Add Team
                        </Button>
                        <Button className="w-full">
                          <Trophy className="mr-2 h-4 w-4" />
                          Add Tournament
                        </Button>
                        <Button className="w-full">
                          <Calendar className="mr-2 h-4 w-4" />
                          Schedule Match
                        </Button>
                      </div>
                    </CardContent>
                  </Card>
                </div>
              </TabsContent>

              <TabsContent value="users">
                <Card>
                  <CardHeader>
                    <CardTitle>User Management</CardTitle>
                    <CardDescription>Manage users and their roles</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <p className="text-gray-500 mb-4">This section will contain user management functionality.</p>
                    <Button>
                      <UserPlus className="mr-2 h-4 w-4" />
                      Add New User
                    </Button>
                  </CardContent>
                </Card>
              </TabsContent>

              <TabsContent value="teams">
                <Card>
                  <CardHeader>
                    <CardTitle>Team Management</CardTitle>
                    <CardDescription>Manage teams and their players</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <p className="text-gray-500 mb-4">This section will contain team management functionality.</p>
                    <Button>
                      <Shield className="mr-2 h-4 w-4" />
                      Add New Team
                    </Button>
                  </CardContent>
                </Card>
              </TabsContent>

              <TabsContent value="tournaments">
                <Card>
                  <CardHeader>
                    <CardTitle>Tournament Management</CardTitle>
                    <CardDescription>Manage tournaments and matches</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <p className="text-gray-500 mb-4">This section will contain tournament management functionality.</p>
                    <Button>
                      <Trophy className="mr-2 h-4 w-4" />
                      Create New Tournament
                    </Button>
                  </CardContent>
                </Card>
              </TabsContent>
            </Tabs>
          </div>
        </main>
      </div>
    </div>
  )
}

function NavItem({ icon, label, href }) {
  const router = useRouter()
  const isActive = router.pathname === href

  return (
    <Button
      variant={isActive ? "default" : "ghost"}
      className={`w-full justify-start ${isActive ? "bg-blue-50 text-blue-700" : ""}`}
      onClick={() => router.push(href)}
    >
      {icon && <span className="mr-2">{icon}</span>}
      {label}
    </Button>
  )
}

function StatCard({ icon, title, value, description }) {
  return (
    <Card>
      <CardContent className="p-6">
        <div className="flex items-center">
          <div className="mr-4">{icon}</div>
          <div>
            <p className="text-sm font-medium text-gray-500">{title}</p>
            <p className="text-3xl font-bold">{value.toLocaleString()}</p>
            <p className="text-sm text-gray-500">{description}</p>
          </div>
        </div>
      </CardContent>
    </Card>
  )
}

function ActivityItem({ icon, title, description, time }) {
  return (
    <div className="flex items-start">
      <div className="mr-4 mt-1">{icon}</div>
      <div className="flex-1">
        <h4 className="text-sm font-medium">{title}</h4>
        <p className="text-sm text-gray-500">{description}</p>
        <p className="text-xs text-gray-400 mt-1">{time}</p>
      </div>
    </div>
  )
}

function StatusItem({ label, status, color }) {
  return (
    <div className="flex items-center justify-between">
      <span className="text-sm">{label}</span>
      <span className={`text-sm font-medium text-${color}-600 flex items-center`}>
        <span className={`h-2 w-2 rounded-full bg-${color}-500 mr-2`}></span>
        {status}
      </span>
    </div>
  )
}

